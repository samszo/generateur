<?php
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tqdev\PhpCrudApi\Cache\Cache;
use Tqdev\PhpCrudApi\Column\ReflectionService;
use Tqdev\PhpCrudApi\Controller\Responder;
use Tqdev\PhpCrudApi\Database\GenericDB;
use Tqdev\PhpCrudApi\Middleware\Router\Router;
use Tqdev\PhpCrudApi\RequestUtils;

class apiStats {

    private $responder;
    private $pdo;

    public function __construct(Router $router, Responder $responder, GenericDB $db, ReflectionService $reflection, Cache $cache)
    {
        $this->pdo=$db->pdo();
        $router->register('GET', '/apiStats/*/*/*', array($this, 'uses'));
        $router->register('GET', '/apiStats/*/*', array($this, 'count'));
        $this->responder = $responder;
    }

    public function count(ServerRequestInterface $request): ResponseInterface
    {
        $table = RequestUtils::getPathSegment($request, 3);
        $sql = 'SELECT COUNT(*) nb FROM '.$table;
        $stmt = $this->query($sql,[]);
        $records = $stmt->fetchAll();
        return $this->responder->success($records[0]['nb']);
    }

    public function uses(ServerRequestInterface $request): ResponseInterface
    {
        $params = RequestUtils::getParams($request);
        $q = RequestUtils::getPathSegment($request, 3);
        $parameters = array();

        switch ($q) {
            case 'oeuvre':
                $parameters[] = RequestUtils::getPathSegment($request, 4);
                $sql= 'select count(distinct odu.id_dico) nbDico
                        , count(distinct c.id_concept) nbConcept 
                    from gen_oeuvres o
                    inner join gen_oeuvres_dicos_utis odu on odu.id_oeu = o.id_oeu
                    inner join gen_dicos d on d.id_dico = odu.id_dico AND d.general = 0
                    left join gen_concepts c on c.id_dico = d.id_dico
                    where o.id_oeu = ?
                    ';
                break;
            
            default:
                # code...
                break;
        }
        $stmt = $this->query($sql, $parameters);
        $records = $stmt->fetchAll();
        return $this->responder->success($records);
    }

    private function query(string $sql, array $parameters): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        //echo "- $sql -- " . json_encode($parameters, JSON_UNESCAPED_UNICODE) . "\n";
        $stmt->execute($parameters);
        return $stmt;
    }

}
