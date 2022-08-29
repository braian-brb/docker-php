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
5. Acceder al contenedor con el id
   ```sh
   docker exec -it <id> bash
   ```
6. Otorgar permisos para que pueda escribir el archivo de cache
   ```sh
   chmod 777 .
   ```


<!-- USAGE EXAMPLES -->
## Usage

Esta aplicacion retorna las cotizaciones del dolar en un unico endpoint GET /cotizaciones.php

http://localhost:4040/cotizaciones.php

## Query params

### dolar 
Para obtener una cotizacion en especifico, se debe enviar el query param `dolar` especificando el tipo de cambio del dolar.

###### Ejemplo
http://localhost:4040/cotizaciones.php/?dolar=blue

######Opciones disponibles:
`oficial`
`blue`
`liqui`
`promedio`
`turista`

### evolucion
Para obtener el historico de una cotizacion en especifico, se debe enviar el query param `evolucion` en true. (Esta disponible solo para el dolar oficial)

###### Ejemplo
http://localhost:4040/cotizaciones.php/?dolar=oficial&evolucion=true

######Opciones disponibles:
`true`
`false`