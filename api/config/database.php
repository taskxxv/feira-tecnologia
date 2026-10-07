<?php
return ['default'=>env('DB_CONNECTION','pgsql'),'connections'=>[
 'pgsql'=>['driver'=>'pgsql','url'=>env('DATABASE_URL'),'host'=>env('DB_HOST','127.0.0.1'),'port'=>env('DB_PORT','5432'),'database'=>env('DB_DATABASE','escola'),'username'=>env('DB_USERNAME','escola'),'password'=>env('DB_PASSWORD',''),'charset'=>'utf8','prefix'=>'','prefix_indexes'=>true,'search_path'=>'public','sslmode'=>'prefer'],
 'sqlite'=>['driver'=>'sqlite','url'=>env('DATABASE_URL'),'database'=>env('DB_DATABASE',database_path('database.sqlite')),'prefix'=>'','foreign_key_constraints'=>true],
]];
