<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div id="countries">

    </div>
</body>

<script>
    try{
        //API REST COUNTRIES
        fetch("https://restcountries.com/v3.1/region/america", {             
            method: "GET", // *GET, POST, PUT, DELETE, etc.
            headers: {
                Accept: "application/json", "Content-Type": "application/json",
            }
        }).then((response) => response.json())
        .then((data) => {  //data es el resultado de la promesa
            console.log(data) ;   //Imprimimos en consola el resultado de la promesa
            let countries = document.getElementById("countries"); //Obtenemos el div con id countries
            data.forEach(element => { //Recorremos el arreglo de objetos
                countries.innerHTML += `
                    <div>
                        <h2>${element.name.common}</h2>
                        <img src="${element.flags.png}" alt="">
                        <p>${element.capital}</p>
                        <p>${element.continents}</p>
                    </div>
                `; //Agregamos al div countries el nombre y la bandera de cada pais
            });

        });
    }catch (error) {
        console.log("Error fetch Login Interno", error);
    }
</script>
</html>