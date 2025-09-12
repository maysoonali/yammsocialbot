<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=h, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div style="border: 3px solid black;">
      <h1>defineApi</h1>  
      <form action="/callExtractor" method="Post">
        @csrf 
        <input name="undifinedAPI" type="text" placeholder="RAWAPI">
        <button>Define</button>
      </form>
    </div>
    
</body>
</html>