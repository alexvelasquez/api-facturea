# API REST FACTUREA
API REST que brinda servicios web de la aplicación Facturea

## Comenzando 🚀

_Estas instrucciones te permitirán obtener una copia del proyecto en funcionamiento en tu máquina local para propósitos de desarrollo y pruebas._

Mira **Deployment** para conocer como desplegar el proyecto.


### Pre-requisitos 📋


```
PHP >= 7.1.3
SYMFONY 4.4
```

### Instalación 🔧

_Clonar_

```
git clone https://github.com/alexvelasquez/api-facturea.git
cd  api-facturea
```

_Ejecutar composer install_

```
php -d memory_limit = -1 composer.phar install
```

_Crear claves publicas y privadas_

```
mkdir -p config\jwt,
openssl genrsa -out config/jwt/private.pem 4096,
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

_Generar DATABASE_
```
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
php bin/console doctrine:migrations:execute 20201120215344
```

_Cargar Provincias y Localidades_
```
Desde POSTMAN ó INSOMNIA

Ejecutar los siguientes endpoint:
- (POST) **{path}/api/login_check** para obtener el token.
- (POST) **{path}/api/datosGeograficos/cargar** ejecuto el endpoint con el token generado anteriormente.


```


## Autores ✒️
* **Velasquez Alex**
