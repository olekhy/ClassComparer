<?php
/**
 * Created by JetBrains PhpStorm.
 * @author Oleksandr Khutoretskyy <olekhy@gmail.com>
 *         Date: 2/5/13
 *         Time: 10:48 PM
 */
namespace ClassComparer;


class Printer
{
    /**
     * @var string
     */
    protected $header;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var array
     */
    protected $blocks;

    /**
     * @var string
     */
    protected $footer;

    protected static $isHeaderPrinted;
    protected static $isBodyPrinted;
    protected static $isFooterPrinted;

    /**
     * @var string  holds new line character
     */
    protected $newLine;

    /**
     * build the printer
     *
     * @param   string  $header
     * @param   string  $footer
     */
    public function __construct($header, $footer)
    {
        static::$isHeaderPrinted = false;
        static::$isBodyPrinted = array();
        static::$isFooterPrinted = false;

        $this->setHeader($header);
        $this->setFooter($footer);

        if (PHP_SAPI === 'cli') {
            $this->newLine = PHP_EOL;
        } else {
            $this->newLine = "\n";
        }
    }

    /**
     * printout
     *
     * @return  string
     */
    public function __toString()
    {

        $string = '';


        if (!static::$isHeaderPrinted) {
            $header = $this->getHeader();

            $string .= $this->nl('', 2) . $this->nl($header);
            $string .= $this->nl(str_repeat('=', strlen($header)), 2);
            static::$isHeaderPrinted = true;
        }

        $body = $this->getBody();
        if (strlen($body) < 1) {

            foreach ($this->getBlocks() as $name => $contents) {

                if (!isset(static::$isBodyPrinted[$name])) {
                    $body .= $this->nl($name, 1);
                    $body .= $this->nl(str_repeat('-', strlen($name)));

                    foreach ($contents as $subContent) {
                        if (is_scalar($subContent)) {
                            $body .= $this->nl("\t" . $subContent);
                            continue;
                        }
                        foreach($subContent as $value) {
                            foreach($value as $key => $myContent) {
                                $body .= $this->nl('');
                                $body .= $this->nl("\t" . $key);
                                foreach ($myContent as $_content) {
                                    $body .= $this->nl("\t" .join($this->newLine, $_content));
                                }
                            }
                        }
                    }
                    $body .= $this->nl('', 2);
                    static::$isBodyPrinted[$name] = true;
                }
            }
        }
        $string .= $this->nl($body, 2);

        if (!static::$isFooterPrinted &&
            array_keys(static::$isBodyPrinted) == array_keys($this->getBlocks())) {
            $footer = $this->getFooter();
            $string .= $this->nl(str_repeat('=', strlen($footer)));
            $string .= $this->nl($footer, 2);
            static::$isFooterPrinted = true;
        }

        return $string;
    }

    /**
     * add content to the stack by name
     *
     * @param $name
     * @param $content
     *
     * @return Printer
     */
    public function addBlock($name, $content)
    {
        $this->blocks[$name][] = $content;
        return $this;
    }

    /**
     * @return array
     */
    public function getBlocks()
    {
        return (array) $this->blocks;
    }

    /**
     * add content block to the stack at first place
     *
     * @param $name
     * @param $content
     *
     * @return Printer
     */
    public function unShitBlock($name, $content)
    {
        $this->blocks = array_merge(array($name => array($content)), $this->blocks);
        return $this;
    }

    /**
     * set content as body
     *
     * @param $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param   string  $footer
     */
    public function setFooter($footer)
    {
        $this->footer = $footer;
    }

    /**
     * @return  string
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * @param string    $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
    }

    /**
     * @return  string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * append number of new line character to the string
     *
     * @param   string  $string
     * @param   int     $num
     *
     * @return string
     */
    public function nl($string, $num = 1)
    {
        return $string . str_repeat($this->newLine, $num);
    }

    /**
     * show progress of completely, could be used in foreach
     *
     * @param   string  $symbol
     * @param   int     $nlAfter
     *
     * @return string
     */
    public function progress($symbol = '.', $nlAfter = 80)
    {
        static $i = 0;

        if (++$i > $nlAfter) {
            $i = 0;
            return $this->newLine;
        };

        return $symbol;
    }
}
