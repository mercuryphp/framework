<?php

namespace System\Web\UI;

class Table extends Element {
   
    protected $source;
    protected $headers = array();
    protected $callbacks = array();
    protected $alternateRowClass = array();
    protected $navigation = array();

    public function __construct($source, array $attributes = array()){
        parent::__construct();
        $this->source = $source;
        $this->attributes = array_merge($this->attributes, $attributes);
        $this->alternateRowClass = array('','');
    }
    
    public function setHeaders(array $headers){
        $this->headers = new \System\Collections\Dictionary($headers);
        return $this;
    }
    
    public function onColumnRender($columnName, $func){
        $this->callbacks['onColumnRender'][$columnName] = $func;
        return $this;
    }
    
    public function setAlternateRowClass($class1, $class2){
        $this->alternateRowClass[0] = $class1;
        $this->alternateRowClass[1] = $class2;
        return $this;
    }
    
    public function addNavigation($title, $href, array $attributes = array()){
        $this->navigation[] = array('title' => $title, 'href' => $href, 'attributes' => $attributes);
        return $this;
    }

    public function render(){
        $control = $this->control
            ->append('<table border=1')
            ->append($this->renderAttributes())
            ->append('>')
            ->append('<thead>');

        $headerString = new \System\Std\String('');
        
        foreach($this->headers as $header){
            $headerString = $headerString->append('<th>'.PHP_EOL)
            ->append($header)
            ->append('</th>');
        }
        
        foreach($this->navigation as $nav){
            $headerString = $headerString->append('<th></th>');
        }
        
        if(count($this->headers) > 0){
            $control = $control->append('<tr>@HEADERS@</tr>'.PHP_EOL)->replace('@HEADERS@', $headerString);
        }

        $control = $control->append('</thead>'.PHP_EOL)
            ->append('<tbody>'.PHP_EOL);

        foreach($this->source as $idx => $row){
            
            $rowString = new \System\Std\String('');
            
            if(is_object($row)){
                $row = \System\Std\Object::getProperties($row);
            }

            foreach($row as $column=>$value){ 
                if($this->headers->getKeys()->contains($column)){
                    if(array_key_exists($column, $this->callbacks['onColumnRender'])
                            && is_callable($this->callbacks['onColumnRender'][$column])){
                        $value = call_user_func_array($this->callbacks['onColumnRender'][$column], array($row));
                    }
                    $rowString = $rowString->append('<td>')->append($value)->append('</td>'.PHP_EOL);
                }
            }
            
            foreach($this->navigation as $nav){
                $rowString = $rowString->append('<td>')->append(new Link($nav['title'], $nav['href'], $nav['attributes'], $row))->append('</td>'.PHP_EOL);
            }
            
            $rowClass = $this->alternateRowClass[$idx % 2] ? ' class="'.$this->alternateRowClass[$idx % 2].'"' : '';
            $control = $control->append('<tr')
                    ->append($rowClass)->append('>@ROW@</tr>'.PHP_EOL)->replace('@ROW@', $rowString);
        }
        
        $control = $control->append('</tbody>')
            ->append('</table>');

        echo $control;
    }
}