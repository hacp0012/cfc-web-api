<!DOCTYPE html>
<html class="h-100" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  {{-- <meta
    name="viewport"
    content="width=device-width, initial-scale=1"
  > --}}
  <meta
    name="viewport"
    content="width=device-width,initial-scale=1,shrink-to-fit=no"
  />

  <title inertia>{{ config('app.name', 'CFC') }}</title>

  <!-- Fonts -->
  {{-- <link
    rel="preconnect"
    href="https://fonts.bunny.net"
  >
  <link
    href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
    rel="stylesheet"
  /> --}}
  {{-- External --}}
  {{-- <meta
    name="viewport"
    content="width=device-width,initial-scale=1,shrink-to-fit=no"
  /> --}}
  <meta
    name="description"
    content="La famille Chretienne"
  />
  <link
    rel="apple-touch-icon"
    sizes="180x180"
    href="/assets/img/apple-touch-icon.png"
  />
  <link
    rel="icon"
    type="image/png"
    sizes="32x32"
    href="/assets/img/favicon-32x32.png"
  />
  <link
    rel="icon"
    type="image/png"
    sizes="16x16"
    href="/assets/img/favicon-16x16.png"
  />
  <link
    rel="icon"
    type="image/png"
    sizes="96x96"
    href="/assets/img/favicon.png"
  />
  <meta
    name="author"
    content="Holger Koenemann"
  />
  <meta
    name="generator"
    content="Eleventy v2.0.0"
  />
  <meta
    name="HandheldFriendly"
    content="true"
  />
  {{-- <title>Stride HTML Template - Frontpage one</title> --}}
  <link
    rel="stylesheet"
    href="/assets/css/theme.min.css"
  />

  <style>
    /* inter-300 - latin */
    @font-face {
      font-family: "Inter";
      font-style: normal;
      font-weight: 300;
      font-display: swap;
      src: local(""), url("/assets/fonts/inter-v12-latin-300.woff2") format("woff2"),
        /* Chrome 26+, Opera 23+, Firefox 39+ */
        url("/assets/fonts/inter-v12-latin-300.woff") format("woff");
      /* Chrome 6+, Firefox 3.6+, IE 9+, Safari 5.1+ */
    }

    @font-face {
      font-family: "Inter";
      font-style: normal;
      font-weight: 500;
      font-display: swap;
      src: local(""), url("/assets/fonts/inter-v12-latin-500.woff2") format("woff2"),
        /* Chrome 26+, Opera 23+, Firefox 39+ */
        url("/assets/fonts/inter-v12-latin-500.woff") format("woff");
      /* Chrome 6+, Firefox 3.6+, IE 9+, Safari 5.1+ */
    }

    @font-face {
      font-family: "Inter";
      font-style: normal;
      font-weight: 700;
      font-display: swap;
      src: local(""), url("/assets/fonts/inter-v12-latin-700.woff2") format("woff2"),
        /* Chrome 26+, Opera 23+, Firefox 39+ */
        url("/assets/fonts/inter-v12-latin-700.woff") format("woff");
      /* Chrome 6+, Firefox 3.6+, IE 9+, Safari 5.1+ */
    }
  </style>
  {{-- End External --}}

  <!-- Scripts -->
  @routes
  @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
  @inertiaHead
</head>

<body data-bs-spy="scroll" data-bs-target="#navScroll" class="font-sans antialiased d-flex h-100 w-100">
  @inertia
</body>

{{-- External --}}
<script src="/assets/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/aos.js"></script>
<script>
  AOS.init({
        duration: 800, // values from 0 to 3000, with step 50ms
      });
</script>

{{-- <script>
  let scrollpos = window.scrollY;
      const header = document.querySelector(".navbar");
      const header_height = header?.offsetHeight ?? 60;

      const add_class_on_scroll = () =>
        header.classList.add("scrolled", "shadow-sm");
      const remove_class_on_scroll = () =>
        header.classList.remove("scrolled", "shadow-sm");

      window.addEventListener("scroll", function () {
        scrollpos = window.scrollY;

        if (scrollpos >= header_height) {
          add_class_on_scroll();
        } else {
          remove_class_on_scroll();
        }

        console.log(scrollpos);
      });
</script> --}}

</html>
