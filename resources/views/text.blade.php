<html lang="en">
<head>
    <style>
        
             
            
       
             html, *{
                margin:0;
                padding:0;
        }
        body{
             font-family:serif;
          
             
        }
        #code{
            font-size:2rem;
            color: #0b048e;
            font-weight:900;
        }
        p{
            color:#adadad;
             font-weight:200;
             font-size:1.2rem;
        }

             
    </style>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>plus Pharmacy</title>
</head>
<body>
   <div  class="container">
          <h3>
            Hello  {{$data["userName"]}},
          </h3>

          <p>
               We have received a request to reset your password,
               please  enter   the code below  to reset your password
          </p>
           <p id="code">
                     {{$data["code"]}}               
        </p>
           <p>
                 If you did not request for a password reset, please ignore this message.
           </p>

           <p>
                 Thank You
           </p>
   </div>  
        
     
</body>
</html>