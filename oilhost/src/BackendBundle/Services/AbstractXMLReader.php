<?php

namespace BackendBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;

class AbstractXMLReader extends XMLValidator
{
    protected $reader;

    protected $result = array();

    protected $_eventStack = array();

    protected $existCurrenciesArray = array();

    protected $existCategoriesArray = array();

    protected $existProductsArray = array();

    protected $xml_path;

    protected $container;

    public function __construct($xml_path, ContainerInterface $container, $options = 0)
    {
        $this->container = $container;
        $this->xml_path = $xml_path;
        libxml_disable_entity_loader(false);

        libxml_use_internal_errors(true);

        if($this->isXMLFileValid($xml_path))
        {
            $this->reader = new \XMLReader();
            $this->reader->open( $this->xml_path, NULL, $options | LIBXML_NOWARNING | LIBXML_PARSEHUGE );
        }
        else
            throw new \Exception( "Oh no, all is going wrong! Bad XML file!" );
    }


    public function parse()
    {
        $this->reader->read();

        while( $this->reader->read() )
        {
            if( $this->reader->nodeType == \XMLREADER::ELEMENT )
            {
                $fnName = 'parse' . $this->reader->localName;

                if( method_exists( $this, $fnName ) )
                {
                    $lcn = $this->reader->name;

                    // event at the beginning of the parsing block
                    $this->fireEvent('beforeParseContainer', array(
                        'name' => $lcn,
                    ));

                    // jogging around the children
                    if( $this->reader->name == $lcn && $this->reader->nodeType != \XMLREADER::END_ELEMENT )
                    {
                        // event before parsing an element
                        $this->fireEvent('beforeParseElement', array(
                            'name' => $lcn,
                        ));

                        // calling parsing function
                        $this->{$fnName}();

                        // event for name block
                        $this->fireEvent($fnName);

                        // event after parsing of element
                        $this->fireEvent('afterParseElement', array(
                            'name' => $lcn,
                        ));
                    }
                    elseif( $this->reader->nodeType == \XMLREADER::END_ELEMENT )
                    {
                        // event at the end of parsing the block
                        $this->fireEvent('afterParseContainer', array(
                            'name' => $lcn,
                        ));
                    }
                }
            }
        }
    }

    public function onEvent($event, $callback)
    {
        if( !isset( $this->_eventStack[$event] ) )
        {
            $this->_eventStack[$event] = array();
        }

        $this->_eventStack[$event][] = $callback;

        return $this;
    }

    public function fireEvent($event, $params = null, $once = false)
    {
        if( $params == null )
        {
            $params = array();
        }
        $params['context'] = $this;

        if( !isset( $this->_eventStack[$event] ) )
        {
            return false;
        }

        $count = count( $this->_eventStack[$event] );

        if( isset( $this->_eventStack[$event] ) && $count > 0 )
        {
            for($i = 0; $i < $count; $i++)
            {
                call_user_func_array( $this->_eventStack[$event][$i], $params );

                if( $once == true )
                {
                    array_splice( $this->_eventStack[$event], $i, 1 );
                }
            }
        }
    }


    public function getResult()
    {
        return $this->result;
    }

    public function clearResult()
    {
        $this->result = array();
    }

    public function moveToStart()
    {
        $this->reader->close();
        return $this->reader->open($this->xml_path);
    }

}