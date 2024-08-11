<?php

namespace App\mobile_v1\handlers;

use App\Models\Otp;
use Illuminate\Support\Str;
use InnerOtpHandler\Generator as InnerOtpHandlerGenerator;
use InnerOtpHandler\Notifier;

class OtpHandler
{
  /**
   * Create a new class instance.
   */
  public function __construct()
  {
    $this->notify = new Notifier;
  }

  public Notifier $notify;

  public function generate(int $length = 4): InnerOtpHandlerGenerator
  {
    $generator = new InnerOtpHandlerGenerator;
    $generator->generate(length: $length);

    return $generator;
  }

  public function check(string|int $otp): bool
  {
    $this->invalidateExpireds();

    $result = Otp::where('otp', $otp)->first();

    if ($result) {
      $result->delete();
      return true;
    }

    return false;
  }

  /** Delete all expired otp's. */
  public function invalidateExpireds(): bool
  {
    $state = otp::where('expire_at', '<', time())->delete();
    return $state;
  }

  private function invalidateAllOf(string $ref): void
  {
    Otp::where('ref', $ref)->delete();
  }

  /** @return array [state:SENT|FAILED, otp, expire_at] */
  static public function sendOtp(string $phoneCode, string $phoneNumber): array
  {
    $expireAt = time() + (60 * 3); // expire in 3Munites.
    $otpHandler = new OtpHandler;
    // SMS OTP MESSAGE :
    $otpMessageText = env('OPT_SMS_TEMPLATE', "La CFC. \nVoici votre code: CFC-[OTP-CODE]");

    $generateOtp = $otpHandler->generate();
    $generateOtp->store(for: $phoneCode . $phoneNumber, expirAt: $expireAt);

    $state = $otpHandler->notify->message(text: $otpMessageText, mask: '[OTP-CODE]', maskReplacer: $generateOtp->otp)->via(
      via: Notifier::SEND_VIA_SMS,
      at: $phoneCode . $phoneNumber,
    );

    // Invalidate all expired's.
    $otpHandler->invalidateExpireds();

    return ['state'=> $state ? 'SENT' : 'FAILED', 'otp' => $generateOtp->otp, 'expire_at' => $expireAt];
  }
}

namespace InnerOtpHandler;

use App\Models\Otp;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class Generator
{
  public string $otp;

  public function generate(int $length = 4): int
  {
    $generated = null;

    $start = '1';
    $end = '9'; { // Generable length.
      --$length;
      for ($len = 0; $len < $length; $len++) {
        $start .= '0';
        $end .= '9';
      }
    }

    while (true) {
      $generated = random_int(intval($start), intval($end));

      // Cheche if exist.
      $founden = Otp::where('otp', $generated)->first();

      if ($founden == null) break;
    }

    $this->otp = $generated;

    return $generated;
  }

  private function invalidateAllOf(string $ref): void
  {
    Otp::where('ref', $ref)->delete();
  }

  /** Store in DB. */
  public function store(string $for, ?int $expirAt = null): void
  {
    $otp = new Otp;

    $this->invalidateAllOf(ref: $for);

    $otp->otp = $this->otp;
    $otp->ref = $for;
    $otp->expire_at = $expirAt ?? time() + (60 * 3); // Default 3 Munites.

    $otp->save();
  }
}

class Notifier
{
  const SEND_VIA_SMS = 'sms';
  const SEND_VIA_MAIL = 'mail';
  public ?string $messageText = null;

  /** Parse message and limite lenght (144 Chars). */
  public function message(string $text, ?string $mask = null, ?string $maskReplacer = null): Notifier
  {
    $maxLength = 144;

    if ($mask) $text = str_replace($mask, $maskReplacer, $text);

    if (Str::length($text) <= $maxLength) {
      $this->messageText = $text;
    } else {
      $this->messageText = Str::limit($text, $maxLength - 3, '...');
    }

    return $this;
  }

  /** Send.
   * @param string $via the protocol to use, to send sms.
   * @param string $at the receiver email or phone number.
   */
  public function via(string $via, string $at): bool
  {
    $state = false;
    if ($via == Notifier::SEND_VIA_SMS) $state = $this->sms($this->messageText, $at);
    elseif ($via == Notifier::SEND_VIA_MAIL) $state = $this->mail($this->messageText, $at);

    return $state;
  }

  private function sms(string $sms, string $phoneNumber): bool
  {
    $url = env('OTP_SMS_API_URL', null);

    $phoneNumber = Str::replace('-', '', $phoneNumber);

    $data = [
      "api_id" => env('OTP_SMS_API_ID', ''),
      "api_password" => env('OTP_SMS_API_PASSWORD', ''),
      "sms_type" => "T",
      "encoding" => "UFS",
      "sender_id" => env('OTP_SMS_API_SENDER_NAME', 'CFC'),
      "phonenumber" => $phoneNumber,
      "textmessage" => $sms,
      // "templateid" => "null",
      // "V1" => null,
      // "V2" => null,
      // "V3" => null,
      // "V4" => null,
      // "V5" => null,
      // "ValidityPeriodInSeconds" => 60,
      // "uid" => "xyz",
      // "callback_url" => "https://xyz.com/",
      // "pe_id" => NULL,
      // "template_id" => NULL
    ];

    if ($url) {
      try {
        $requestResponse = Http::post(url: $url, data: $data);
        /*  RESPONSE FORMAT :
          {
            "message_id": 4125,
            "status": "S", // 'S' : success | 'F' : Failed
            "remarks": "Message Submitted Successfully" ,
            “uid”: “xyz”
          }
        */

        if ($requestResponse->successful()) {
          $response = $requestResponse->json(key: 'status', default: null);

          if ($response && $response == 'S') return true;
          else return false;
        }
      } catch (Exception $e) {
        return true; // Defaultly.
      }
    }
    return false;
  }

  private function mail(string $email, string $at): bool
  {
    return false;
  }
}
