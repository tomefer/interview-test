# Prueba técnica - Backend

# Enunciado

Imaginemos que un cliente finaliza un pedido mediante la aplicación móvil y ésta nos lanza una llamada a la API REST para almacenarlo en la base de datos.

El pedido debe contener:

- Nombre y apellidos del cliente
- Email (Único por cliente)
- Teléfono
- Dirección de entrega (solo puede existir una por pedido)
- Fecha de compra
- Fecha de entrega
- Franja de hora seleccionada para la entrega
- Productos seleccionados con sus atributos (al menos 5 productos)
  - Nombre
  - Descripción
  - Unidades
  - Precio
  - Tienda de venta
- Importe total

Por otro lado, están nuestro shoppers que, mediante su aplicación, necesitan obtener la parte del pedido de la tienda donde está comprando. 
Es necesario contar con un endpoint que reciba como parámetro el ID del shopper y el ID de la tienda donde está comprando 
y nos devuelva un JSON con la información del pedido asignado y los productos que tiene que comprar.

# TODO
[ ] Arquitectura de aplicación en Symfony
[ ] Construir el modelo de datos en MYSQL con todas las entidades y relaciones
[ ] Endpoint para persistir el pedido en BD
[ ] Endpoint para mostrar los productos que debe comprar el shopper
# Evaluable
- Diseño modelado de datos
- API REST con sus endpoints
- Arquitectura de aplicación en Symfony
- Utilización del ORM
- Uso de buenas prácticas
- Patrones de diseño utilizados
- Optimización del performance
# Entrega

Mediante subida del ejercicio a Bitbucket/Github/… o enviando un email con el código a tech@lolamarket.com

# Resolución del ejercicio

## Instalación del proyecto en local
El proyecto incluye un stack con docker de php7, mysql, phpmyadmin y elk.
Para arrancar, primero copiarse los ficheros de parámetros y de variables de entorno:
```bash
$ cp .env.dist .env
$ cp app/config/parameters.yml.dist app/config/parameters.yml
```
Es importante actualizar el valor de SYMFONY_APP_PATH en el fichero .env
SYMFONY_APP_PATH=/Users/ftome/lola-market-test

Luego levantar docker con 
```bash
$ docker-compose up -d
```
Entrar en el contenedor de php
```
$ docker-compose exec php bash
```
Y ejecutar los comandos para iniciar el proyecto:
```
$ composer install
$ php bin/console doctrine:schema:create 
$ php bin/console doctrine:fixtures:load
 ```

El proyecto estará corriendo en localhost:80. En localhost:8080 hay un phpmyadmin 
y en localhost:81 un kibana donde visualizar los logs.

He utilizado este repositorio para configurar el entorno: 
https://github.com/maxpou/docker-symfony

## Petición de crear pedido
Tened en cuenta los ids de producto, de shopper, clientAddres etc que se le pasan, ya que si esos ids no existen en la
base de datos te dará un error.
```
POST /clientOrder HTTP/1.1
Host: localhost
{
	"order" : {
		"shopId" : 3,
		"clientAddress" : 6,
		"selectedDeliveryDate" : "2018-03-09 20:00:00",
		"lines" : [
			{"productId" : 209, "quantity" : 2 },
			{"productId" : 210, "quantity" : 2 },
			{"productId" : 211, "quantity" : 2 },
			{"productId" : 212, "quantity" : 2 },
			{"productId" : 213, "quantity" : 2 }
		]
	}
}
```

## Respuesta cuando ocurre algún error:
```
{
    "success": false,
    "errors": "Product not found"
}
```
## Respuesta cuando se crea el pedido correctamente
```
{
    "success": true,
    "data": {
        "id": 10,
        "client": {},
        "address": {},
        "price": "3000",
        "shopperAssignments": {},
        "purchaseDate": {
            "date": "2018-03-04 01:24:02.215089",
            "timezone_type": 3,
            "timezone": "Europe/Paris"
        },
        "selectedDeliveryDate": {
            "date": "2018-03-09 20:00:00.000000",
            "timezone_type": 3,
            "timezone": "Europe/Paris"
        },
        "deliveredAt": null,
        "status": 1,
        "updatedAt": {
            "date": "2018-03-04 01:24:02.215019",
            "timezone_type": 3,
            "timezone": "Europe/Paris"
        }
    }
}
```

## Petición de obtener los productos asignados a un shopper
Se puede adjuntar el parametro shopId para obtener la respuesta filtrada por tienda o si no se indica el parámetro 
el endpoint devuelve los productos de todas las tiendas para el shopper
```
GET /shopper/14/assignments?shopId=9 HTTP/1.1
Host: localhost
```
## Respuesta
```
[
    {
        "clientOrder": {
            "id": 2,
            "totalPrice": "30000",
            "selectedDeliveryDate": {
                "date": "2018-03-03 23:06:58.000000",
                "timezone_type": 3,
                "timezone": "Europe/Paris"
            },
            "purchaseDate": {
                "date": "2018-03-03 23:06:58.000000",
                "timezone_type": 3,
                "timezone": "Europe/Paris"
            },
            "orderLines": [
                {
                    "clientOrderLineid": 4,
                    "quantity": 1,
                    "price": "140",
                    "productData": {
                        "id": 260,
                        "shopId": 9,
                        "price": "530",
                        "photoId": null,
                        "description": "Pasta"
                    },
                    "shop": "Lidl"
                }
            ]
        }
    }
]
```


##Analisis entidades:

- Client (id, email, name, lastname, phone, updated_at) 
- ClientAddress (id, client_id, street_type, address, postal_code, city, country, updated_at)

- Order (id, client_id, address_id, price, purchase_date, selected_delivery_date, delivered_at, status, updated_at)
- Shop(id, name, description, updated_at)
- Product(id, shop_id, price, name, description, photo_id, created_at, status, updated_at)
- OrderLine(id, order_id, product_id, quantity, price, promotion_id, updated_at)
- Shopper(id, name, email, updated_at)
- ShopperAssignment(order_id, shop_id, status, updated_at)

###Comentarios:

ClientAddresses se puede complicar todo lo que uno quiera, listado de paises, tipos de via, marcar dirección por defecto... etc

Asumo que el precio siempre es en euros

He separado productos en products y en order line ya que los productos pueden ir cambiando de precio, pero el order line
marcará el precio que tenga ese producto en el momento de hacer el pedido. Además un OrderLine puede tener un precio 
especial, por ejemplo si compras 2x1 en cierto producto o aplicas cualquier descuento se vería reflejado ahi. El order line
referenciará siempre al producto del que partió.
 
Los productos se añaden nuevos cada vez que el proceso que actualiza el catálogo se ejecutase siempre y cuando cambiase algo, 
cambiando el status del producto que sustituye y manteniendo un status especifico para los productos que están ofertados. 
Esto potencialmente puede suponer tener millones de productos antiguos en la base de datos, si llegase a suponer un 
problema de rendimiento, yo probaria primero a añadir un índice por estado, cachear el catalogo de productos activos y 
si nada de esto lo soluciona tocaría llevarse los productos extra a una tabla tipo ProductHistory, lo malo de esto es 
que la clave ajena ya no nos serviría y la complejidad aumentaría. Otra posible opciçon sería asumir que lo que cambia en un 
producto más habitualmente sea el precio y modelaríamos este en otra tabla a parte a modo de histórico de precios, haciendo así
que la tabla de productos no crezca tan rápidamente

#Comentarios de la implementación

- Gestionar dinero no es sencillo, a priori, habría que tener en cuenta diferentes currencies y el modo de almacenar dichas currencies,
he decidido almacenar la cantidad de dinero en string ignorando la currency y utilizar la implementación del patrón Money 
sugerida por Martin Fowler http://moneyphp.org/

- No estoy teniendo en cuenta impuestos tipo IVA etc, por lo que el precio del los orders lo calculo como la suma 
del numero de productos comprados

- No he implementado ningun sistema de autenticación.

- He utilizado el entity manager para el endpoint de obtener los productos de una tienda para un shopper, pero desde el punto de vista
de la performance es mejor utilizar una query y sacar toda la inforación de ahi. 





