<?php
    namespace App;

    use App\Utils\HttpException;

    class Router {
        private array $routes = [];

        public function __construct() {
            $this->add_route('OPTIONS', '.*', function(){return null;});
        }

        private function create_pattern(string $path): string {
            $pattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<$1>[^/]+)', $path);
            $pattern = "#^$pattern$#";
            return $pattern;
        }

        private function add_route(string $method, string $path, callable|array $callback): void {
            $this->routes[$method][$this->create_pattern($path)] = $callback;
        }

        public function get(string $path, callable|array $callback): void {
            $this->add_route('GET', $path, $callback);
        }

        public function post(string $path, callable|array $callback): void {
            $this->add_route('POST', $path, $callback);
        }

        public function put(string $path, callable|array $callback): void {
            $this->add_route('PUT', $path, $callback);
        }

        public function delete(string $path, callable|array $callback): void {
            $this->add_route('DELETE', $path, $callback);
        }

        private function resolve(): mixed {
            $method = $_SERVER['REQUEST_METHOD'];
            $path = $_SERVER['REQUEST_URI'] ?? '/';
            $path = explode('?', $path)[0];
            
            foreach ($this->routes[$method] ?? [] as $pattern=>$callback) {
                if (preg_match($pattern, $path, $matches)) {
                    $params = array_map(
                        fn($v) => urldecode($v),
                        array_filter(
                            $matches,
                            fn($key) => !is_numeric($key),
                            ARRAY_FILTER_USE_KEY
                        )
                    );

                    if (is_array($callback)) {
                        [$class, $method] = $callback;
                        $controller = new $class();
                        return $controller->$method(...array_values($params));
                    }

                    return $callback(...array_values($params));
                }
            }

            http_response_code(404);
            echo json_encode(['detail'=>'404 Not Foud']);
            return null;
        }

        private function errors(int $status_code, \Throwable $exception){
            http_response_code($status_code);
            echo json_encode(['detail'=>$exception->getMessage()]);
            return null;
        }

        public function run():mixed {
            try{
                return $this->resolve();
            }catch(\TypeError $e){
                return $this->errors(422, $e);
            }catch(HttpException $e){
                return $this->errors($e->getStatusCode(), $e);
            }catch(\Throwable $e){
                return $this->errors(500, $e);
            }
        }
    }
?>