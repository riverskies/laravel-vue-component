<?php

use Philo\Blade\Blade;
use RiverSkies\Laravel\BladeDirectiveInterface;
use RiverSkies\Laravel\VueComponentDirective;

class VueComponentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Blade template engine instance.
     * @var Blade
     */
    protected $blade;

    /**
     * Set up function.
     */
    public function setUp() {
        parent::setUp();

        $this->blade = $this->setUpTemplateEngine(new VueComponentDirective);
    }

    /** @test */
    public function it_ignores_the_tags_if_the_specified_variable_is_not_defined()
    {
        $html = $this->blade->view()->make('test')->render();

        $this->assertEquals('<h1>Testing</h1>', $this->clean($html));
    }

    /** @test */
    public function it_returns_the_component_tags_if_vue_component_variable_is_set()
    {
        $html = $this->blade->view()->make('test')->with(['vueComponent' => 'test-comp'])->render();

        $this->assertEquals(
            '<component is="test-comp" inline-template v-cloak><h1>Testing</h1></component>',
            $this->clean($html)
        );
    }

    /** @test */
    public function it_returns_the_component_tag_if_vue_component_variable_is_array()
    {
        $data = [
            'vueComponent' => [
                'is' => 'test-comp'
            ],
        ];

        $html = $this->blade->view()->make('test')->with($data)->render();

        $this->assertEquals(
            '<component is="test-comp" inline-template v-cloak><h1>Testing</h1></component>',
            $this->clean($html)
        );
    }

    /** @test */
    public function it_adds_the_data_parameter_to_the_component()
    {
        $data = [
            'vueComponent' => [
                'is' => 'test-comp',
                'data' => [
                    'value' => 123
                ]
            ],
        ];

        $html = $this->blade->view()->make('test')->with($data)->render();

        $this->assertEquals(
            '<component is="test-comp" data="JSON.parse(decodeURIComponent(\'' . rawurlencode(json_encode($data['vueComponent']['data'])) . '\'))" inline-template v-cloak><h1>Testing</h1></component>',
            $this->clean($html)
        );
    }

    /**
     * Minifying HTML content.
     *
     * @link http://stackoverflow.com/questions/5312349/minifying-final-html-output-using-regular-expressions-with-codeigniter#answer-5324014
     *
     * @param $data
     * @return mixed
     */
    private function clean($data)
    {
        $regexp = '%# Collapse whitespace everywhere but in blacklisted elements.
        (?>             # Match all whitespaces other than single space.
          [^\S ]\s*     # Either one [\t\r\n\f\v] and zero or more ws,
        | \s{2,}        # or two or more consecutive-any-whitespace.
        ) # Note: The remaining regex consumes no text at all...
        (?=             # Ensure we are not in a blacklist tag.
          [^<]*+        # Either zero or more non-"<" {normal*}
          (?:           # Begin {(special normal*)*} construct
            <           # or a < starting a non-blacklist tag.
            (?!/?(?:textarea|pre|script)\b)
            [^<]*+      # more non-"<" {normal*}
          )*+           # Finish "unrolling-the-loop"
          (?:           # Begin alternation group.
            <           # Either a blacklist start tag.
            (?>textarea|pre|script)\b
          | \z          # or end of file.
          )             # End alternation group.
        )  # If we made it here, we are not in a blacklist tag.
        %Six';

        return preg_replace($regexp, "", $data);
    }

    /**
     * Creates the context.
     *
     * @return array
     */
    private function createTestWorld()
    {
        list($resource, $view, $cache) = $this->getDirectories();

        @mkdir($resource);
        @mkdir($cache);
        @mkdir($view);

        @file_put_contents($view . '/test.blade.php', '
            @vue($vueComponent)
                <h1>Testing</h1>
            @endvue
        ');

        return [$view, $cache];
    }

    /**
     * Sets up template engine to mimic Laravel.
     *
     * @param BladeDirectiveInterface $directive
     * @return Blade
     */
    private function setUpTemplateEngine(BladeDirectiveInterface $directive)
    {
        list($views, $cache) = $this->createTestWorld();
        $blade = new Blade($views, $cache);

        $blade->getCompiler()->directive(
            $directive->openingTag(), [$directive, 'openingHandler']
        );

        $blade->getCompiler()->directive(
            $directive->closingTag(), [$directive, 'closingHandler']
        );

        return $blade;
    }

    /**
     * Tear down function.
     */
    public function tearDown()
    {
        list($resource, $view, $cache) = $this->getDirectories();

        $this->deleteDirectory($view);
        $this->deleteDirectory($cache);
        $this->deleteDirectory($resource);
    }

    /**
     * Helper to set the directories.
     *
     * @return array
     */
    private function getDirectories()
    {
        $resource = __DIR__ . '/../resources';
        $view = __DIR__ . '/../resources/views';
        $cache = __DIR__ . '/../resources/cache';

        return array($resource, $view, $cache);
    }

    /**
     * Delete a directory with recursive check.
     *
     * @param $dir
     * @return bool
     */
    private function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($dir);
    }
}