<?php
/**
 * Created by PhpStorm.
 * User: dlouvard_imac
 * Date: 06/01/2017
 * Time: 22:31
 */

namespace Dlouvard\Bootformlaravel;

use \Form;
use Illuminate\Support\Facades\App;
use curunoir\translation\Models\Locale;

class BootForm
{
    private $placeholder = false;
    private $horizontal = false;
    private $columns = ['left' => 'col-sm-3', 'right' => 'col-sm-9'];

    public function __construct($form, $session)
    {
        $this->form = $form;
        $this->session = $session;
        $this->model = null;
    }

    /**
     * @param null $model
     * @param array $options
     * @return mixed
     */
    public function open($model = null, array $options = array())
    {
        $this->model = $model;
        $controller_name = str_plural($this->get_single_class($model)) . 'Controller';
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

        return $this->form->model($model, $options);
    }

    /**
     * @param $type
     * @param $name
     * @param null $label
     * @param null $value
     * @param array $options
     * @param array $list
     * @return string
     */
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
                if (isset($options['left'])) $this->columns['left'] = $options['left'];
                $return .= $this->form->label($name, htmlspecialchars($label) . $required, ['class' => $this->columns['left'] . ' control-label']);
            } else {
                $return .= $this->form->label($name, $label . $required, ['class' => 'control-label']);
            }
        }
        //Horizontal
        if ($this->horizontal) {
            if (isset($options['right'])) $this->columns['right'] = $options['right'];
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
        } elseif ($type == 'datalists') {
            $tmp = '<input list="' . $name . '" name="' . $name . '" value="'.$value.'" class="form-control">';
            $tmp .= '<datalist id="' . $name . '">';
            foreach ($list as $l):
                $tmp .= '<option value="'.$l.'" ' . ($value == $l ? 'selected="selected"' : null) . '>' . $l . '</option>';
            endforeach;
            $tmp .= '</datalist>';

            $return .= $tmp;
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

    /**
     * @param $type
     * @param $name
     * @param null $label
     * @param array $options
     * @return string
     */
    public function inputLang($type, $name, $label = null, $options = array())
    {
        $options['locales'] = Locale::getAll();
        $errors = $this->session->get('errors');

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

            if ($this->horizontal || isset($options['horizontal'])) {
                if (isset($options['left'])) $this->columns['left'] = $options['left'];
                $return .= $this->form->label($name, $label . $required, ['class' => $this->columns['left'] . ' control-label']);
            } else {
                $return .= $this->form->label($name, $label . $required, ['class' => 'control-label']);
            }
        }
        //Horizontal
        if ($this->horizontal) {
            if (isset($options['right'])) $this->columns['right'] = $options['right'];
            $return .= '<div class="' . $this->columns['right'] . ' addoncustom">';
        } else {
            $return .= '<div class="addoncustom">';
        }

        foreach ($options['locales'] as $l):
            $pTrad = [
                'locale_id' => $l->id,
                'model' => get_class($this->model),
                'field' => $name
            ];

            $display = $l->code == App::getLocale() ? '' : 'none';
            $return .= '<div class="input-group" data-locale_id="' . $l->id . '" style="display:' . $display . '">';
            $classL = $l->id > 1 ? 'gooTrad' : null;
            //$return .= '<span class="input-group-addon ' . $classL . '" style="min-width:42px"><img width="25px" src="'.asset('/assets/img/flags/' . $l->code . '.png').'"></span>';
            $return .= $this->spanFlag($l->code, $classL);
            unset($options['locales']);
            $trad = $this->model ? $this->model->getTrad($pTrad) : null;
            $options['data-name'] = $name;
            $options['data-type'] = $type == 'textarea' ? $type : 'input';
            if ($type == 'textarea') {
                $return .= $this->form->textarea($name . '[' . $l->id . ']', $trad, $options);
            } else {
                $return .= $this->form->input($type, $name . '[' . $l->id . ']', $trad, $options);
            }
            if ($l->code == App::getLocale())
                $return .= '<span class="input-group-addon showAllTrad ' . $classL . '"><i class="fa fa-flag"></i></span>';

            $return .= '</div>';
        endforeach;

        //Horizontal
        $return .= '</div>';

        $return .= '</div>';
        if ($errors && $errors->has($name)) {
            $return .= '<p class="help-block text-right">' . $errors->first($name) . '</p>';
        }
        return $return;
    }

    /**
     * Returns span containing vendor locale flag if exists
     * @param $locale
     * @param $classL
     */
    public function spanFlag($locale, $classL = null)
    {
        if (file_exists(public_path('/assets/img/vendor/flags/' . $locale . '.png')))
            return '<span class="input-group-addon ' . $classL . '" style="min-width:42px"><img width="25px" src="'.asset('/assets/img/vendor/flags/' . $locale . '.png').'"></span>';
        return '';
    }

    /**
     * @param $name
     * @param null $label
     * @param null $value
     * @param array $options
     * @return string
     */
    public function text($name, $label = null, $value = null, $options = array())
    {
        return $this->input('text', $name, $label, $value, $options);
    }

    /**
     * @param $name
     * @param null $label
     * @param array $options
     * @return string
     */
    public function textLang($name, $label = null, $options = array())
    {
        return $this->inputLang('text', $name, $label, $options);
    }

    /**
     * @param $name
     * @param null $label
     * @param null $value
     * @param array $options
     * @return string
     */
    public function textareaLang($name, $label = null, $value = null, $options = array())
    {
        return $this->inputLang('textarea', $name, $label, $value, $options);
    }

    /**
     * @param $name
     * @param null $label
     * @param null $value
     * @param array $options
     * @return string
     */
    public function file($name, $label = null, $value = null, $options = array())
    {
        return $this->input('file', $name, $label, $value, $options);
    }

    /**
     * @param $name
     * @param null $label
     * @param null $value
     * @param array $options
     * @return string
     */
    public function email($name, $label = null, $value = null, $options = array())
    {
        return $this->input('email', $name, $label, $value, $options);
    }

    /**
     * @param $name
     * @param null $label
     * @param null $value
     * @param array $options
     * @return string
     */
    public function password($name, $label = null, $value = null, $options = array())
    {
        return $this->input('password', $name, $label, $value, $options);
    }

    /**
     * @param $name
     * @param null $label
     * @param null $value
     * @param array $options
     * @return string
     */
    public function checkbox($name, $label = null, $value = null, $options = array())
    {
        return $this->input('checkbox', $name, $label, $value);
    }

    /**
     * @param $name
     * @param null $label
     * @param null $value
     * @param array $list
     * @param array $options
     * @return string
     */
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

    /**
     * @param $name
     * @param null $label
     * @param null $value
     * @param array $list
     * @param array $options
     * @return string
     */
    public function datalists($name, $label = null, $value = null, $list = array(), $options = array())
    {
        return $this->input('datalists', $name, $label, $value, $options, $list);
    }

    /**
     * @param $name
     * @param null $label
     * @param null $value
     * @param array $options
     * @return string
     */
    public function textarea($name, $label = null, $value = null, $options = array())
    {
        return $this->input('textarea', $name, $label, $value, $options);
    }

    /**
     * @param null $name
     * @param bool $modal
     * @param null $value
     * @param string $size
     * @return string
     */
    public function submit($name = null, $modal = true, $value = null, $size = 'normal')
    {
        if (!$name) {
            $name = trad('Valider');
        }
        $size = ($size == 'normal') ? null : $size;
        if ($value) {
            return '<div class="form-submit"><button type="submit" class="btn ' . $size . ' btn-success" name="action" value="' . $value . '" data-action="false">' . $name . '</button></div>';
        }

        if (!$modal):
            return '<div class="form-submit"><button type="submit" class="btn ' . $size . ' btn-success" data-action="false">' . $name . '</button></div>';
        else:
            return '<div class="form-submit"><button type="submit" class="btn ' . $size . ' btn-success">' . $name . '</button></div>';
        endif;
    }

    /**
     * @return mixed
     */
    public function close()
    {
        return $this->form->close();
    }

    function get_single_class($model)
    {
        if ($model) {
            return substr(get_class($model), strrpos(get_class($model), '\\') + 1);
        }
    }

}
