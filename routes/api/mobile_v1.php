<?php

use Illuminate\Support\Facades\Route;

require __DIR__ . '/mobile_v1/test.php';
require __DIR__ . '/mobile_v1/photo.php';
require __DIR__ . '/mobile_v1/file.php';
require __DIR__ . '/mobile_v1/auth.php';
require __DIR__ . '/mobile_v1/misc.php';
require __DIR__ . '/mobile_v1/user.php';
require __DIR__ . '/mobile_v1/family.php';
require __DIR__ . '/mobile_v1/teaching.php';
require __DIR__ . '/mobile_v1/com.php';
require __DIR__ . '/mobile_v1/echo.php';
require __DIR__ . '/mobile_v1/comment.php';
require __DIR__ . '/mobile_v1/home.php';
require __DIR__ . '/mobile_v1/calendar.php';
require __DIR__ . '/mobile_v1/find_cfc_around.php';
require __DIR__ . '/mobile_v1/favorites.php';

// * -- ADMIN -- *
require __DIR__ . '/admin_feature/admin.php';

// require __DIR__ . '/../quest.php';

// * -- FEATURES -- *
Route::prefix('feature')->group(function() {
  require __DIR__ . '/mobile_v1/ws.php';
  require __DIR__ . '/mobile_v1/otp.php';
  require __DIR__ . '/mobile_v1/validable.php';
  require __DIR__ . '/mobile_v1/notification.php';
  require __DIR__ . '/mobile_v1/search.php';
});
