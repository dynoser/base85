<?php
namespace dynoser\base85;

use \PHPUnit\Framework\TestCase;

class vc85Test extends TestCase
{
    protected $object;

    public $man_str = 'Man is distinguished, not only by his reason, but by this singular '
                    . 'passion from other animals, which is a lust of the mind, that by a '
                    . 'perseverance of delight in the continued and indefatigable generation '
                    . 'of knowledge, exceeds the short vehemence of any carnal pleasure.';

    public $man_85_utf = '9jqoлBlbDЭBleB1DJфжфFЩfцqю0JhKFГGLгCjФя4GpДd7FЯцL7ФГ6Фщю0JDEFГGПГфEVд2FЯц
ЮГDJфжяФГжK0ФГ6LЩDfЭx0Ec5eБDffZЩEZeeяBlя9pFvAGXBPCsiфDGmгФ3BBюFжЦЮCAfu2юAKY
iЩDИbдФFDцжщфCшUэФ3BNЖEcYf8ATD3sФqЪdДAftVqChШNqFГGд8фEVдяфCfгЭFD5W8ARlolDИa
lЩDИdГjФГЪ3rФдFПaфD58wATD4ДBlФl3DeдцЭDJsй8ARoFbю0JMKФqB4лFЯцRГAKZЦЭDfTqBGПG
гuDяRTpAKYowфCTю5фCeiЖDИИЪЩEц9щoFж2M7юc';

    public $man_85_vwx = '9jqo^BlbD-BleB1DJ+*+F(f,q/0JhKF<GL>Cj@.4Gp$d7F!,L7@<6@)/0JDEF<G%<+EV:2F!,O<
DJ+*.@<*K0@<6L(Df-x0Ec5e;DffZ(EZee.Bl.9pFvAGXBPCsi+DGm>@3BB/F*&OCAfu2/AKYi(
DIb:@FD,*)+C]U=@3BN#EcYf8ATD3s@q?d$AftVqCh[NqF<G:8+EV:.+Cf>-FD5W8ARlolDIal(
DId<j@<?3r@:F%a+D58wATD4$Bl@l3De:,-DJs`8ARoFb/0JMK@qB4^F!,R<AKZ&-DfTqBG%G>u
D.RTpAKYow+CT/5+Cei#DII?(E,9)oF*2M7/c';

    protected function setUp(): void {
        $this->object = new vc85();
    }

    /**
     * @covers dynoser\base85\vc85::init
     */
    public function testInit()
    {
        vc85::init(1);
        $ch = vc85::$vc85enc[0];
        $this->assertEquals('!', $ch);
        $ch = vc85::$vc85enc[1];
        $this->assertEquals('"', $ch);
        $ch = vc85::$vc85enc[6];
        $this->assertEquals("'", $ch);
        $ch = vc85::$vc85enc[59];
        $this->assertEquals("\\", $ch);

        $dec = vc85::$vc85dec[175];
        $this->assertEquals(0, $dec);
        $dec = vc85::$vc85dec[118];
        $this->assertEquals(1, $dec);
        
        vc85::init(2);
        $ch = vc85::$vc85enc[0];
        $this->assertEquals('!', $ch);
        $ch = vc85::$vc85enc[1];
        $this->assertEquals('v', $ch);
        $ch = vc85::$vc85enc[6];
        $this->assertEquals("w", $ch);
        $ch = vc85::$vc85enc[59];
        $this->assertEquals("x", $ch);

        vc85::init(3);
        $ch = vc85::$vc85enc[0];
        $this->assertEquals("Я", $ch);
        $dec = vc85::$vc85dec[175];
        $this->assertEquals(0, $dec);
        
        $ch = vc85::$vc85enc[75];
        $this->assertEquals("Л", $ch);
        $dec = vc85::$vc85dec[108];
        $this->assertEquals(75, $dec);
    }
    
    public function replacesProvider() {
        vc85::init(3);
        foreach(['!Я','#Ж','$Д','%П','&Ц','(Щ',')щ','*ж','+ф',',ц','-Э','.я',
            '/ю',':д',';Б','<Г','=э','>г','?Ъ','@Ф','IИ','OЮ','[Ш',']ш','^л', '`й', 'lЛ'] as $repl) {
            $fromNum = \ord($repl[0]) - 33;
            $toChar = \substr($repl, 1);
            $toNum = \ord(\substr($repl, -1));
            yield [$fromNum, $toChar, $toNum];
        }
    }
    
    /**
     * @covers dynoser\base85\vc85::init
     * @dataProvider replacesProvider
     */
    public function testReplaces($fromNum, $toChar, $toNum) {
        $chEnc = vc85::$vc85enc[$fromNum];
        $this->assertEquals($toChar, $chEnc, "Case: $fromNum");
        $decCh = vc85::$vc85dec[$toNum];
        $this->assertEquals($fromNum, $decCh);
    }
    
    
    /**
     * @covers dynoser\base85\vc85::__construct
     */
    public function testConstruct()
    {
        $bc = new vc85(3, 0);

        $str = "One day I'm gonna fly away One day when heavens calls my name";

        $enc = $bc->encode($str);
        $exp = "<~дMs_dA79aф8LJЪtB5_ШжФ3BюuGpДЮгФГitkDИjrДФГiuгBЮuwЩBЮtUqASuQ3Фps1iFЯцэFфDtVщAH~>";
        $this->assertEquals($exp, $enc);

        $back = $bc->decode($enc);       
        $this->assertEquals($str, $back);
        
        new vc85(2);

        $enc = $bc->encode($str);
        $exp = '<~:Ms_dA79a+8LJ?tB5_[*@3B/uGp$O>@<itkDIjr$@<iu>BOuw(BOtUqASuQ3@ps1iF!,=F+DtV)AH~>';
        $this->assertEquals($exp, $enc);
        $back = $bc->decode($enc);       
        $this->assertEquals($str, $back);

        new vc85(2, 0);

        $vwx_line = \str_replace(["\n","\r"], ['',''], $this->man_85_vwx);

        $enc = $bc->encode($this->man_str);
        $exp = '<~' . $vwx_line . '~>';;
        $this->assertEquals($exp, $enc);
        $back = $bc->decode($enc);       
        $this->assertEquals($this->man_str, $back);

        new vc85(null, 0, false);

        $enc = $bc->encode($this->man_str);
        $exp = $vwx_line;
        $this->assertEquals($exp, $enc);
        $back = $bc->decode($enc);       
        $this->assertEquals($this->man_str, $back);
        
        new vc85(1, 0);

        $enc = $bc->encode($str);
        $exp = ':Ms_dA79a+8LJ?tB5_[*@3B/uGp$O>@<itkDIjr$@<iu>BOu\'(BOtUqASuQ3@ps1iF!,=F+DtV)AH';
        $this->assertEquals($exp, $enc);
        $back = $bc->decode($enc);       
        $this->assertEquals($str, $back);
        
        new vc85(1, 0, true);

        $enc = $bc->encode($str);
        $exp = '<~:Ms_dA79a+8LJ?tB5_[*@3B/uGp$O>@<itkDIjr$@<iu>BOu\'(BOtUqASuQ3@ps1iF!,=F+DtV)AH~>';
        $this->assertEquals($exp, $enc);
        $back = $bc->decode($enc);       
        $this->assertEquals($str, $back);
    }


    /**
     * @covers dynoser\base85\vc85::encode
     */
    public function testEncode()
    {
        $o = $this->object;

        vc85::init(2);
                
        // empty string test
        vc85::$addPf = false;
        $str = '';
        $enc = $o->encode($str);
        $this->assertEquals('', $enc);

        vc85::$addPf = true;
        $enc = $o->encode($str);
        $this->assertEquals('<~~>', $enc);

        $vwx_line = \str_replace(["\n","\r"], ['',''], $this->man_85_vwx);
        // pre-defined strings test
        vc85::$splitWidth = 75;
        vc85::$addPf = false;
        $src = $this->man_str;
        $enc = $o->encode($src);
        $chk_enc = \str_replace(["\n","\r"], ['',''], $enc);
        $exp = $vwx_line;
        $this->assertEquals($exp, $chk_enc);
        $back = $o->decode($enc);       
        $this->assertEquals($this->man_str, $back);

        // random strings test
        for($len = 1; $len < 258; $len++) {
            for($i=0;$i<5;$i++) {
                $str = random_bytes($len);
                $enc = $o->encode($str);
                $dec = $o->decode($enc);
                $this->assertEquals($str, $dec, "Differrent: $enc\n len = $len, case = $i \n".bin2hex($str)."\n" . bin2hex($dec));
            }
        }
    }
    
    public function randomVars() {
        // random strings test
        foreach([0, 5, 15, 30, 75] as $splitWidth) {
            foreach([true, false] as $addPf) {
                foreach([1,2,3] as $encodeMode) {
                    for($len = 1; $len < 32; $len++) {
                        $str = random_bytes($len);
                        yield [$encodeMode, $splitWidth, $addPf, $str];
                    }
                }
            }
        }
    }
    
    /**
     * @covers dynoser\base85\vc85::decode
     * @dataProvider randomVars
     */
    public function testDecode($encodeMode, $splitWidth, $addPf, $rndStr)
    {
        vc85::init($encodeMode, $splitWidth, $addPf);
        $enc = vc85::encode($rndStr);
        $dec = vc85::decode($enc);
        $this->assertEquals($rndStr, $dec);
    }
    
    /**
     * @covers dynoser\base85\vc85::decode
     */
    public function testDecodeException()
    {
        $this->expectException('\InvalidArgumentException');
        $dec = vc85::decode($this->man_85_utf . 'неть!');
    }
    
    
    /**
     * @covers dynoser\base85\vc85::explodeUTF8
     */
    public function testExplodeUTF8() {
        vc85::$mbstrsplit = false;
        $un = vc85::explodeUTF8('Да!是的');
        $this->assertEquals(['Д', 'а', '!', '是', '的'], $un);
        
        $un = vc85::explodeUTF8(chr(209) . chr(208));
        $this->assertFalse($un);
    }
    
    /**
     * @covers dynoser\base85\vc85::implodeSplitter
     */
    public function testImplodeSplitter() {
        vc85::$mbstrsplit = false;
        $enc = vc85::encode($this->man_str);
        $dec = vc85::decode($enc);
        $this->assertEquals($this->man_str, $dec);
    }
    
    
}
