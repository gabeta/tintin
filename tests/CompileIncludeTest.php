<?php

use Tintin\Tintin;
use Tintin\Compiler;
use Tintin\Loader\Filesystem;

class CompileIncludeTest extends \PHPUnit\Framework\TestCase
{
    use CompileClassReflection;

    /**
     * @var Filesystem
     */
    private Filesystem $loader;

    /**
     * On setup
     */
    public function setUp(): void
    {
        $this->loader = new Filesystem([
          'path' => __DIR__ . '/view',
          'extension' => 'tintin.php',
          'cache' => __DIR__ . '/cache'
        ]);
    }

    /**
     * Test configuration
     */
    public function testConfiguration()
    {
        $this->assertInstanceOf(Filesystem::class, $this->loader);

        $instance = new Tintin($this->loader);

        $this->assertInstanceOf(Tintin::class, $instance);
    }

    public function testCompileIncludeStatement()
    {
        $compiler = new Compiler();
        $compileInclude = $this->makeReflectionFor('compileInclude');
        $render = $compileInclude->invoke($compiler, "%include('filename')");

        $this->assertEquals($render, "<?php echo \$__tintin->getStackManager()->includeFile('filename', ['__tintin' => \$__tintin]); ?>");
    }

    public function testCompileIncludeStatementWithParam()
    {
        $compiler = new Compiler();
        $compileInclude = $this->makeReflectionFor('compileInclude');
        $render = $compileInclude->invoke($compiler, "%include('filename', ['name' => 'Bow'])");

        $this->assertEquals($render, "<?php echo \$__tintin->getStackManager()->includeFile('filename', ['name' => 'Bow'], ['__tintin' => \$__tintin]); ?>");
    }

    public function testCompileIncludeStatementWithParamComplex()
    {
        $compiler = new Compiler();
        $compileInclude = $this->makeReflectionFor('compileInclude');
        $render = $compileInclude->invoke($compiler, "%include('filename', ['name' => 'Bow', 'is_admin' => isset(\$is_admin)])");

        $this->assertEquals($render, "<?php echo \$__tintin->getStackManager()->includeFile('filename', ['name' => 'Bow', 'is_admin' => isset(\$is_admin)], ['__tintin' => \$__tintin]); ?>");
    }

    public function testCompileIncludeStatementWithParamComplexUsage()
    {
        $template = <<<TEMPLATE
%include('filename', [
    'name' => 'Bow',
    'is_admin' => isset(\$is_admin)
])
TEMPLATE;

        $render_out = <<<TEMPLATE
<?php echo \$__tintin->getStackManager()->includeFile('filename', [
    'name' => 'Bow',
    'is_admin' => isset(\$is_admin)
], ['__tintin' => \$__tintin]); ?>
TEMPLATE;

        $compiler = new Compiler();
        $compileInclude = $this->makeReflectionFor('compileInclude');
        $render = $compileInclude->invoke($compiler, $template);

        $this->assertEquals($render, $render_out);
    }
}
