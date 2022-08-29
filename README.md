# Introduccion
Esta API retorna las cotizaciones del dolar en un unico endpoint GET 

/cotizaciones.php 

Cuenta con una estrategia de cache para no procesar la informacion cada vez que se solicite.

### Instalacion

1. Clonar el repo
   ```sh
   git clone https://github.com/braian-brb/docker-php
   ```
2. Construir imagen docker
   ```sh
   docker build -t test-php-cotizaciones .
   ```
3. Correr la imagen en un contenedor
   ```js
   docker run -p 4040:80 -d -v $(pwd)/src:/var/www/html/ test-php-cotizaciones
   ```
4. Ver los contenedores (para ver el id del contenedor)
   ```sh
   docker ps
   ```
5. Acceder a la consola del contenedor con el id
   ```sh
   docker exec -it <id> bash
   ```
6. Otorgar permisos para que pueda escribir el archivo de cache
   ```sh
   chmod 777 .
   ```
7. Salir de la consola del contenedor
   ```sh
   exit
   ```

<!-- USAGE EXAMPLES -->
## Uso

http://localhost:4040/cotizaciones.php

## Query params

### dolar 
Para obtener una cotizacion en especifico, se debe enviar el query param `dolar` con el tipo de cambio seleccionado.

###### Ejemplo
http://localhost:4040/cotizaciones.php/?dolar=blue

###### Opciones disponibles:
`oficial`
`blue`
`liqui`
`promedio`
`turista`

### evolucion
Para obtener el historico del dolar oficial, se debe enviar el query param `evolucion` en true. (solo para el dolar oficial)

###### Ejemplo
http://localhost:4040/cotizaciones.php/?dolar=oficial&evolucion=true

###### Opciones disponibles:
`true`
`false`