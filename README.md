/api/movies
Se obtiene la lista completa de peliculas por get, a las cuales se les puede aplicar un acción de alta por post.

/api/movies/id
Se obtiene una pelicula especifica por get a la cual se le puede aplicar una accion de baja o modificación por delete y put.

Opcionales:

/api/movies?genero=genenerodeTablageneros
Filtra por un genero especifico ya inlcuido en la tabla generos.

/api/movies?sort=campoTablaPeliculas&order=asc/desc
Ordena por un campo especifico de la tabla peliculas de forma ascendende o descendente. Puede incluirse solo uno de los dos parametros.

/api/movies?pag=1&limit=2
Realiza una paginación. Ambos parametros son necesarios para funcionar.

/api/auth/token
Con el user "user@user.com" y la contraseña "12345" se obtiene un token de acceso.
