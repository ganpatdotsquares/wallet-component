<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   @if(config('GET.main_favicon'))
   <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/photo/' . config('GET.main_favicon')) }}">
   @else
   <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('backend/assets/images/favicon.png') }}">
   @endif
   <title>{{config('GET.system_application_name')}} |
      {{ $page && $page->title ? $page->title : '' }}
   </title>
   <link rel="stylesheet" href="{{ asset('css/style.css') }}"> <!-- Link to your CSS file -->
   <style>
      /* Basic styling for the privacy policy page */
      body {
         font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
         line-height: 1.6;
         background-color: #f4f4f4;
         margin: 0;
         padding: 0;
         color: black;
         /* Set text color to black */
      }

      /* Header Styles */
      header {
         background: #03a9f3;
         /* Primary color */
         color: white;
         padding: 20px 0;
         text-align: center;
         position: relative;
      }

      header .mylogo {
         width: 150px;
         /* Logo size */
         margin-bottom: 10px;
         /* Space below logo */
      }

      header h1 {
         margin: 0;
         font-size: 2.5em;
         /* Increased font size */
         font-weight: 600;
         /* Bold font weight */
         color: black;
         /* Set text color to black for the title */
      }

      /* Mid-section Styles */
      .mid-section {
         background: white;
         padding: 40px;
         margin: 20px 0;
         border-radius: 10px;
         /* More rounded corners */
         box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
         /* More pronounced shadow */
         text-align: left;
         /* Left align text */
      }

      .about-section {
         padding: 20px;
      }

      h4 {
         color: #555;
         /* Grey color for coming soon text */
         margin: 20px 0;
      }

      /* Footer Styles */
      footer {
         background: #03a9f3;
         /* Footer background color */
         color: white;
         text-align: center;
         padding: 20px 0;
         position: relative;
         bottom: 0;
         width: 100%;
      }

      footer p {
         margin: 5px 0;
         font-size: 0.9em;
         /* Smaller font size */
      }
   </style>
</head>

<body>

   <header>
      @if(config('GET.main_logo'))
      <img src="{{ asset('assets/photo/' . config('GET.main_logo')) }}" alt="homepage" class="mylogo">
      @else
      <img src="{{ asset('backend/assets/images/dots.png') }}" alt="homepage" class="mylogo" />
      @endif
      <h1>{{ $page && $page->title ? $page->title : 'Page Title' }}</h1> <!-- Updated Title -->
   </header>

   <div class="about-section z-index2 pad-tb50 relative content-page">
      <div class="container">
         <div class="mid-section">
            @if(isset($page) && $page->description)
            {!! $page->description !!}
            @else
            <h4>Coming Soon..</h4>
            @endif
         </div>
      </div>
   </div>

   <footer class="footer text-center">
      <p>&copy; {{ date('Y') }} {{ config('GET.system_application_name') }}. All rights reserved.</p>
   </footer>

</body>

</html>