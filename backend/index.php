<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>ArcTouch Backend API</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <!-- Favicon -->
  <link rel="apple-touch-icon" sizes="180x180" href="../frontend/dist/img/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="../frontend/dist/img/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="../frontend/dist/img/favicon-16x16.png">
  <link rel="manifest" href="../frontend/dist/site.webmanifest">
</head>
<body>
  <div class="container-fluid">
    <h1>Endpoint basic usage:</h1>
    <ul>
      <li><b>upcoming</b>: This endpoint retrieves a list with the first 20 entries (basic data) of the upcoming movies. <a href="upcoming/" target="_blank" alt="Click here to try">DEMO</a></li>
      <li><b>upcoming/{page}</b>: This endpoint retrieves a list with the 20 entries (basic data) of the upcoming movies, starting from {page} (which is 1 by default). <a href="upcoming/2" target="_blank" alt="Click here to try">DEMO</a></li>
      <li><b>movie/{id}</b>: This endpoint retrieves the full data of a given movie ID (positive integer). <a href="movie/27205" target="_blank" alt="Click here to try">DEMO</a></li>
      <li><b>search/{terms}</b>: This endpoint retrieves a list with the first 20 entries (basic data) of the upcoming movies (based on a given search term, which is a string). <a href="search/war" target="_blank" alt="Click here to try">DEMO</a></li>
      <li><b>search/{terms}/{page}</b>: This endpoint retrieves a list with the 20 entries (basic data) of the upcoming movies (based on a given search term, which is a string), starting from {page} (which is 1 by default). <a href="search/war/3" target="_blank" alt="Click here to try">DEMO</a></li>
    </ul>
  </div>
</body>
</html>