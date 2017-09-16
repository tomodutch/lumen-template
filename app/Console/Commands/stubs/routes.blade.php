$router->get('/{{$plural}}', '{{$pascalCase}}Controller@index');
$router->get('/{{$plural}}/{id}', '{{$pascalCase}}Controller@show');
$router->put('/{{$plural}}/{id}', '{{$pascalCase}}Controller@update');
$router->delete('/{{$plural}}/{id}', '{{$pascalCase}}Controller@destroy');
$router->post('/{{$plural}}', '{{$pascalCase}}Controller@store');
