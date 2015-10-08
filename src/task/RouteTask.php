<?php

namespace sndsgd\http\task;

use \DateTime;
use \RecursiveDirectoryIterator as RDI;
use \RecursiveIteratorIterator as RII;
use \ReflectionClass;
use \SplFileInfo;
use \sndsgd\Classname;
use \sndsgd\Env;
use \sndsgd\Field;
use \sndsgd\field\BooleanField;
use \sndsgd\field\StringField;
use \sndsgd\field\rule\PathTestRule;
use \sndsgd\field\rule\RequiredRule;
use \sndsgd\field\rule\ClosureRule;
use \sndsgd\field\rule\MaxValueCountRule;
use \sndsgd\fs\Dir;
use \sndsgd\fs\File;
use \sndsgd\http\model\Route;


class RouteTask extends \sndsgd\Task
{
    const DESCRIPTION = "A utility for managing routes";
    const VERSION = "1.0.0";

    /**
     * @var array<string,\sndsgd\http\model\Route>
     */
    protected $routes = [];

    /**
     * @var array<string,\sndsgd\user\model\Role>
     */
    protected $userRoles = [];

    /**
     * {@inheritdoc}
     */
    public function __construct(array $fields = [])
    {
        parent::__construct($fields);
        $this->addFields([
            (new StringField("app-directory"))
                ->addAliases("a")
                ->setDescription("the app directory")
                ->addRules([
                    new RequiredRule,
                    new MaxValueCountRule(1),
                    new PathTestRule(Dir::EXISTS | Dir::READABLE)
                ]),
            (new StringField("filename"))
                ->addAliases("f")
                ->setDescription("the resulting filename relative to the app directory")
                ->addRules([
                    new MaxValueCountRule(1),
                    new ClosureRule(function() {
                        $dir = $this->collection->exportFieldValue("app-directory");
                        $file = new File("$dir/{$this->value}");
                        $file->normalize();
                        if (!$file->canWrite()) {
                            $this->message = $file->getError();
                            return false;
                        }
                        $this->value = $file;
                        return true;
                    })
                ]),
            (new StringField("search-dir"))
                ->addAliases("d")
                ->setDescription("directories to search for request handlers")
                ->setExportHandler(Field::EXPORT_ARRAY)
                ->addRules([
                    new RequiredRule,
                    new PathTestRule(Dir::EXISTS | Dir::READABLE)
                ])
        ]);
    }

    public function run()
    {
        $this->opts = $this->exportValues();
        $this->search();

        if ($this->opts["filename"]) {
            Env::log("updating routes config... ");
            $contents = $this->createRouteConfigContents();
            $file = new File($this->opts["filename"]);
            if (!$file->write($contents)) {
                Env::log("\n");
                Env::err("failed to update routes; ".$file->getError()."\n");
            }
            Env::log("@[green]done@[reset]\n");

            # require the router to update the cache file
            $router = require $file->getPath();
        }
    }

    private function loadUserRoles($doctrine)
    {
        // $query = $doctrine->createQuery(
        //    "SELECT r FROM genome\\model\\user\\Role r"
        // );
        // $results = $query->getResult();
        // foreach ($results as $role) {
        //    $this->userRoles[$role->getDescription()] = $role;
        // }
    }

    /**
     * Find routes by examining the filesystem
     * 
     * @return array<genome\model\Route>
     */
    private function search()
    {
        $tmp = [];
        foreach ($this->opts["search-dir"] as $dir) {
            Env::log("searching $dir...\n");
            $iterator = new RII(new RDI($dir, RDI::SKIP_DOTS), RII::SELF_FIRST);
            foreach ($iterator as $file) {
                if (($route = $this->getRouteFromFile($file))) {
                    $combo = $route->getMethod().":".$route->getPath();
                    if ($this->routeExists($route)) {
                        Env::log("@[yellow] ~@[reset] $combo\n");
                    }
                    else {
                        Env::log("@[green] +@[reset] $combo\n");
                        // $doctrine->persist($route);
                        // $doctrine->flush();
                    }
                    $tmp[$combo] = $route;
                }
            }
        }

        $this->routes = $tmp;
    }

    /**
     * @param SplFileInfo $file The file to test
     * @return \genome\model\Route|null
     */
    private function getRouteFromFile(SplFileInfo $file)
    {
        if (
            $file->isFile() &&
            strcasecmp("php", $file->getExtension()) === 0 &&
            ($file = new File($file->getRealPath())) &&
            ($contents = $file->read()) &&
            ($class = Classname::fromContents($contents))
        ) {
            $rc = new ReflectionClass($class);
            if (!$rc->isAbstract() && $rc->isSubclassOf("sndsgd\\http\\inbound\\Request")) {
                return Route::createFromClassname($class);
            }
        }
        return null;
    }

    private function routeExists(Route $route)
    {
        // $doctrine = Storage::getInstance()->get("doctrine");
        // $model = get_class($route);
        // $query = $doctrine->createQuery(
        //    "SELECT r FROM $model r 
        //    WHERE r.path = :path AND r.method = :method"
        // );
        // $query->setParameter("path", $route->getPath());
        // $query->setParameter("method", $route->getMethod());
        // return $query->getOneOrNullResult();
    }

    private function createRouteConfigContents()
    {
        $date = (new DateTime)->format('Y-m-d \a\t H:i:s');
        $content = 
            "<?php\n\n".
            "namespace FastRoute;\n\n".
            "# generated by ".__CLASS__." on $date\n\n".
            "return cachedDispatcher(function(RouteCollector \$r) {\n\n";

        foreach ($this->routes as $route) {
            $method = var_export($route->getMethod(), true);
            $path = var_export($route->getPath(), true);
            $handler = var_export($route->getHandler(), true);
            $content .= "    \$r->addRoute($method, $path, $handler);\n";
        }

        $cacheFile = str_replace(
            $this->opts["app-directory"], 
            "", 
            $this->opts["filename"]
        );

        $content .= 
            "\n}, [\n".
            "    'cacheFile' => APP_DIR.'$cacheFile.cache',\n".
            "    'cacheDisabled' => ENVIRONMENT !== 'prod',\n".
            "]);\n";
        return $content;
    }
}
