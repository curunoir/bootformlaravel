<?php
/**
 * Created by PhpStorm.
 * User: dlouvard_imac
 * Date: 06/01/2017
 * Time: 22:31
 */

namespace Dlouvard\Bootformlaravel;

use \Form;

class BootForm
{
    private $placeholder = false;
    private $horizontal = false;
    private $columns = ['left' => 'col-sm-3', 'right' => 'col-sm-9'];

    public function __construct($form, $session)
    {
        $this->form = $form;
        $this->session = $session;
    }

    public function open($model = null, array $options = array())
    {
        $controller_name = str_plural(get_single_class($model)) . 'Controller';
        if ($model && $model->id) {
            $options['method'] = 'PUT';
            if (!isset($options['action'])) {
                $options['action'] = ["$controller_name@update", _c($model->id)];
            }
        } elseif ($model) {
            if (!isset($options['action'])) {
                $options['action'] = ["$controller_name@store"];
            }
        } else {
            $options['action'] = null;
        }
        if (isset($options['placeholder']) && $options['placeholder'] === true) {
            $this->placeholder = true;
        }

        if (isset($options['class']) && $options['class'] == 'form-horizontal') {
            $this->horizontal = true;

            if (isset($options['columns'])):
                $this->columns = $options['columns'];
                unset($options['columns']);
            endif;
        }
        if (isset($options['model'])) {
            $model = $options['model'];
        }

        //dd($this->columns['left']);

        return $this->form->model($model, $options);
    }

    public function input($type, $name, $label = null, $value = null, $options = array(), $list = array())
    {
        $errors = $this->session->get('errors');
        if (is_array($label)) {
            $options = $label;
            $label = null;
        }
        if (is_array($value)) {
            $options = $value;
            $value = null;
        }

        if ($this->placeholder) {
            $options['placeholder'] = $label;
            $label = false;
        }
        if (!$label) {
            $label = trans("form.$name");
        }

        if (isset($options['class'])) {
            $options['class'] .= ' form-control';
        } else {
            $options['class'] = 'form-control';
        }
        $return = '<div class="form-group form-' . $type . ($errors && $errors->has($name) ? ' has-error' : '') . '">';
        if ($label !== false) {
            if (isset($options['required'])) {
                $required = "*";
            } else {
                $required = null;
            }

            if ($this->horizontal) {
                $return .= $this->form->label($name, $label . $required, ['class' => $this->columns['left'] . ' control-label']);
            } else {
                $return .= $this->form->label($name, $label . $required, ['class' => 'control-label']);
            }
        }
        //Horizontal
        if ($this->horizontal) {
            $return .= '<div class="' . $this->columns['right'] . '">';
        }
        if (isset($options['addon'])):
            $return .= '<div class="input-group">';
            $return .= '<span class="input-group-addon"><i class="fa ' . $options['addon'] . '"></i></span>';
        endif;


        if ($type == 'textarea') {
            $return .= $this->form->textarea($name, $value, $options);
        } elseif ($type == 'select') {
            $return .= $this->form->select($name, $list, $value, $options);
        } elseif ($type == 'checkbox') {
            $return .= $this->form->checkbox($name, 1, $value, $options);
        } else {
            $return .= $this->form->input($type, $name, $value, $options);
        }

        //Horizontal
        if ($this->horizontal) {
            $return .= '</div>';
        }

        //ADDON
        if (isset($options['addon'])):
            $return .= '</div>';

        endif;
        if ($errors && $errors->has($name)) {
            $return .= '<p class="help-block text-right">' . $errors->first($name) . '</p>';
        }


        $return .= '</div>';
        return $return;
    }

    public function text($name, $label = null, $value = null, $options = array())
    {
        return $this->input('text', $name, $label, $value, $options);
    }

    public function file($name, $label = null, $value = null, $options = array())
    {
        return $this->input('file', $name, $label, $value, $options);
    }

    public function email($name, $label = null, $value = null, $options = array())
    {
        return $this->input('email', $name, $label, $value, $options);
    }

    public function password($name, $label = null, $value = null, $options = array())
    {
        return $this->input('password', $name, $label, $value, $options);
    }

    public function checkbox($name, $label = null, $value = null, $options = array())
    {
        return $this->input('checkbox', $name, $label, $value);
    }

    public function select($name, $label = null, $value = null, $list = array(), $options = array())
    {
        if (is_array($label)) {
            $list = $label;
            $label = null;
        }
        if (is_array($value)) {
            $list = $value;
            $value = null;
        }
        return $this->input('select', $name, $label, $value, $options, $list);
    }

    public function textarea($name, $label = null, $value = null, $options = array())
    {
        return $this->input('textarea', $name, $label, $value, $options);
    }

    public function submit($name = null, $modal = true, $value=null, $size='normal')
    {
        if (!$name) {
            $name = trad('Valider');
        }
        $size = ($size == 'normal') ? null : $size;
        if ($value) {
            return '<div class="form-submit"><button type="submit" class="btn '.$size.' btn-success" name="action" value="'.$value.'" data-action="false">' . $name . '</button></div>';
        }

        if (!$modal):
            return '<div class="form-submit"><button type="submit" class="btn '.$size.' btn-success" data-action="false">' . $name . '</button></div>'; else:
            return '<div class="form-submit"><button type="submit" class="btn '.$size.' btn-success">' . $name . '</button></div>';
        endif;
    }



    public function close()
    {
        return $this->form->close();
    }
}
