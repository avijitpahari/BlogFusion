<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        * {
            font-size: large;
        }
    </style>
</head>

<body id="body">
    <div>this is my event click</div><br>

    <div id="a">rsdfznfksukzjnjn</div><br>

    <div>fjsnjkFNjnaznrklnkl</div><br>
    <button id="btn" onclick="mode_change()">change on dark mode</button>
    <script>
        function mode_change() {
            const element = document.querySelector("#body");
            const btn = document.querySelector("#btn");

            if (element.style.backgroundColor === "black") {
                Object.assign(element.style, {
                    backgroundColor: "white",
                    color: "black",
                    
                });
                document.getElementById("a").style.color="black"
                btn.innerHTML="change on dark mode";
            } else {
                Object.assign(element.style, {
                    backgroundColor: "black",
                    color: "white",
                    
                });
                document.getElementById("a").style.color="red"
                btn.innerHTML="change on light mode";
            }
        }

    </script>
</body>

</html>