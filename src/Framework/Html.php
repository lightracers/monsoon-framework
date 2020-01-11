<?php

namespace Framework;

/*
 * HTML utilities class. Many of the methods are static
 */

class Html
{
    public static function validationSummary(?Validator $validatorObject)
    {
        if (is_object($validatorObject) === false || $validatorObject->hasErrors() === false) {
            return null;
        }

        $validationErrors = $validatorObject->getAllErrors();

        return ' <div class="p-t-15 alert alert-danger"><p style="text-align:left"> &bull; &nbsp; '.implode('<br /> &bull; &nbsp; ', $validationErrors).'</p></div>';
    }

    // ckeditor
    public static function ckeditorBasic($formElementName, $editorHeight = '200', $projectGUID = null)
    {
        // http://ckeditor.com/latest/samples/plugins/toolbar/toolbar.html
        return "<script>
        CKEDITOR.replace( \"" . $formElementName . "\" , {
                    height:" . $editorHeight . ",

            // Define the toolbar groups as it is a more accessible solution.
            toolbar: [
                         { name: 'document', groups: [ 'mode' ], items: [ 'Maximize', 'Source'] },
                         { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
                        { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize','TextColor', 'BGColor'  ] },
                        { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock',  ] },
                        { name: 'insert', items: [  'Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', ] },
                        { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
                        { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Subscript,Superscript,Anchor,Specialchar',
                        filebrowserBrowseUrl : '" . BASE_URL . "/assets/kcfinder/browse.php?opener=ckeditor&type=files&project=" . $projectGUID . "',
                        filebrowserImageBrowseUrl :'" . BASE_URL . "/assets/kcfinder/browse.php?opener=ckeditor&type=images&project=" . $projectGUID . "',
                        filebrowserUploadUrl : '" . BASE_URL . "/assets/kcfinder/upload.php?opener=ckeditor&type=files&project=" . $projectGUID . "',
                        filebrowserImageUploadUrl : '" . BASE_URL . "/assets/kcfinder/upload.php?opener=ckeditor&type=images&project=" . $projectGUID . "',
                 } );
    </script>";
    }

    public static function ckeditorSimple($formElementName, $imagesBrowserPath = null, $imagesUploadPath = null, $editorHeight = '200')
    {
        // http://ckeditor.com/latest/samples/plugins/toolbar/toolbar.html
        return "<script>
        CKEDITOR.replace( \"" . $formElementName . "\" , {
                    height:" . $editorHeight . ",

            // Define the toolbar groups as it is a more accessible solution.
            toolbar: [
                         { name: 'document', groups: [ 'mode' ], items: [ 'Maximize', 'Source'] },
                         { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', '-', 'RemoveFormat' ] },
                        { name: 'styles', items: [ 'FontSize'  ] },
                        { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: ['JustifyLeft', 'JustifyCenter', 'JustifyRight', '-', 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent',    ] },
                        { name: 'insert', items: [  'Table', 'HorizontalRule', ] },
                        { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },

            ],
            // Remove the redundant buttons from toolbar groups defined above.
            removeButtons: 'Subscript,Superscript,Anchor,Specialchar'," . ($imagesBrowserPath !== '' ? "filebrowserImageBrowseUrl : '" . $imagesBrowserPath . "',\n" : "") . ($imagesUploadPath !== '' ? "filebrowserImageUploadUrl : '" . $imagesUploadPath . "',\n" : "") . " } );
    </script>";
    }

    // form
    public static function formDummyField($labelText, $value)
    {
        if ($labelText === null) {
            $string = '<div class="form-group row">
                   <p class="col-xs-12">' . $value . '</p>
                </div>';
        } else {
            $string = '<div class="form-group row">
                    <p class="col-sm-3"><label>' . $labelText . '</label></p>
                    <p class="col-sm-8">  ' . $value . '</p>
                </div>';
        }

        return $string;
    }

    public static function formTextbox($labelText, $fieldName, $defaultValue = '', $placeholderValue = '', $requiredField = false, $extras = '')
    {
        if ($labelText === null) {
            $string = '<div class="form-group row">
                   <p class="col-xs-12">
                        <input type="text" class="form-control" placeholder="' . $placeholderValue . '" id="' . $fieldName . '" name="' . $fieldName . '" value="' . $defaultValue . '" ' . ($requiredField === true ? 'required="required"' : '') . ' ' . $extras . ' /></p>
                </div>';
        } else {
            $string = '<div class="form-group row">
                    <p class="col-sm-3"><label for="' . $fieldName . '" ' . ($requiredField === true ? ' class="required"' : '') . '>' . $labelText . '</label></p>
                    <p class="col-sm-8">
                        <input type="text" class="form-control" placeholder="' . $placeholderValue . '" id="' . $fieldName . '" name="' . $fieldName . '" value="' . $defaultValue . '" ' . ($requiredField === true ? 'required="required"' : '') . ' ' . $extras . '/></p>
                </div>';
        }

        return $string;
    }

    public static function formPassword($labelText, $fieldName, $defaultValue = '', $placeholderValue = '', $requiredField = false)
    {
        if ($labelText === null) {
            $string = "\n" . '<div class="form-group row">
    <div class="col-sm-12">
        <input type="password" class="form-control" placeholder="' . $placeholderValue . '" id="' . $fieldName . '" name="' . $fieldName . '" value="' . $defaultValue . '" /></div>
</div>' . "\n";
        } else {
            $string = "\n" . '<div class="form-group row">
    <p class="col-sm-3 "><label for="' . $fieldName . '" ' . ($requiredField !== false ? ' class="required"' : '') . '>' . $labelText . '</label></p>
    <p class="col-sm-8">
        <input type="password" class="form-control" placeholder="' . $placeholderValue . '" id="' . $fieldName . '" name="' . $fieldName . '" value="' . $defaultValue . '"  ' . ($requiredField === true ? ' required="required"' : '') . ' /></p>
</div>' . "\n";
        }

        return $string;
    }

    public static function formSelect($labelText, $fieldName, $keyIndexedValuesArray = [], $selectedKey = null, $requiredField = false)
    {
        if ($labelText !== '') {
            $string = '<div class="form-group row">
                    <p class="col-sm-3"><label for="' . $fieldName . '"' . ($requiredField === true ? ' class="required"' : '') . '>' . $labelText . '</label></p>
                    <p class="col-sm-8">';
        } else {
            $string = '<div class="form-group">';
        }

        $string .= ' <select class="form-control" id="' . $fieldName . '" name="' . $fieldName . '" ' . ($requiredField === true ? 'required="required"' : '') . '>';
        if (is_array($keyIndexedValuesArray) === true) {
            foreach ($keyIndexedValuesArray as $key => $value) {
                $selected = null;
                if ($selectedKey === $key) {
                    $selected = ' selected ';
                }

                if (is_array($value) === true) {
                    $string .= '<optgroup label="' . $key . '">' . "\n";
                    foreach ($value as $optionKey => $optionValue) {
                        $string .= '<option value="' . $optionKey . '" ' . $selected . '>' . $optionValue . '</option>' . "\n";
                    }

                    $string .= '</optgroup>' . "\n";
                } else {
                    $string .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>' . "\n";
                }
            }
        }

        $string .= '</select>';
        $string .= ($labelText !== '' ? '</div>' : '') . '</p>';
        return $string;
    }

    public static function formTextarea($labelText, $fieldName, $defaultValue = '', $placeholderValue = '', $requiredField = false, $rows = 3)
    {
        if ($labelText !== '') {
            $string = '<div class="form-group row">
                        <p class="col-md-3"><label for="' . $fieldName . '"' . ($requiredField === true ? ' class="required"' : '') . '>' . $labelText . '</label></p>
                        <p class="col-md-8">
                            <textarea class="form-control" rows="' . $rows . '" id="' . $fieldName . '" name="' . $fieldName . '" placeholder="' . $placeholderValue . '" ' . ($requiredField === true ? 'required="required"' : '') . '>' . $defaultValue . '</textarea></p>
                    </div>';
        } else {
            $string = '<div class="form-group row">
                       <p class="col-md-12">
                            <textarea class="form-control" rows="' . $rows . '" id="' . $fieldName . '" name="' . $fieldName . '" placeholder="' . $placeholderValue . '" ' . ($requiredField === true ? 'required="required"' : '') . '>' . $defaultValue . '</textarea></p>
                    </div>';
        }

        return $string;
    }

    public static function formDateTextbox($labelText, $fieldName, $defaultValue = '', $dateFormat = 'dd-MMM-yyyy', $requiredField = false)
    {
        if ($labelText !== '') {
            $string = '<div class="form-group row">
                <div class="col-sm-3 col-sm-3"><label for="' . $fieldName . '"' . ($requiredField === true ? ' class="required"' : '') . '>' . $labelText . '</label></div>
                <div class="col-sm-8">
                    <div class="input-group ">
                        <input type="text" class="form-control" id="' . $fieldName . '" name="' . $fieldName . '" value="' . $defaultValue . '" placeholder="' . $dateFormat . '"  ' . ($requiredField === true ? 'required="required"' : '') . '>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>';
        } else {
            $string = '<div class="input-group ">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control inline" id="' . $fieldName . '" name="' . $fieldName . '" value="' . $defaultValue . '" placeholder="' . $dateFormat . '"  ' . ($requiredField === true ? 'required="required"' : '') . '>
                    </div>';
        }

        return $string;
    }

    public static function formTimeTextbox($labelText = '', $fieldName = '', $defaultValue = '', $placeholderValue = 'hh:mm', $requiredField = false)
    {
        if ($labelText !== '') {
            $string = '<div class="form-group row">
                    <p class="col-sm-3"><label for="' . $fieldName . '" ' . ($requiredField === true ? ' class="required"' : '') . '>' . $labelText . '</label></p>
                    <p class="col-sm-8">
                        <input type="text" class="form-control" placeholder="'.$placeholderValue.'" id="' . $fieldName . '" name="' . $fieldName . '" value="' . $defaultValue . '" ' . ($requiredField === true ? 'required="required"' : '') . ' /></p>
                </div>';
        } else {
            $string = '<div class="form-group row">
                    <p class="input-group bootstrap-timepicker timepicker">
                        <input type="text" class="form-control" id="' . $fieldName . '" name="' . $fieldName . '" value="' . $defaultValue . '" placeholder="'.$placeholderValue.'" ' . ($requiredField === true ? 'required="required"' : '') . ' />
                        <span class="input-group-addon">
                            <i class="fa fa-clock-o"></i>
                        </span>
                    </p>
                </div>';
        }

        return $string;
    }

    public static function formColorPicker($labelText = '', $fieldName = '', $defaultValue = '', $placeholderValue = '', $requiredField = false)
    {
        return "\n" . ' <div class="form-group row">
                        <p class="col-sm-3">
                            <label for="' . $fieldName . '" ' . ($requiredField !== false ? ' class="required"' : '') . '>' . $labelText . ' </label>
                        </p>
                        <p class="col-sm-8">
                            <span class="input-group ' . $fieldName . '-colorpicker colorpicker-element">
                                <input type="text" class="form-control" name="' . $fieldName . '" id="' . $fieldName . '" value="' . $defaultValue . '" placeholder="'.$placeholderValue.'" ' . ($requiredField === true ? 'required="required"' : '') . ' />
                                <span class="input-group-addon">
                                    <i style="background-color: ' . $defaultValue . ';"></i>
                                </span>
                            </span>
                        </p>
                    </div>' . "\n";
    }

    public static function formHidden($fieldName, $value)
    {
        return '<input type="hidden" name="' . $fieldName . '" id="' . $fieldName . '" value="' . $value . '" />' . "\n";
    }

    public static function formSubmit($textOnButton, $fieldName)
    {
        $string = '<div class="form-group row">
                    <div class="col-sm-2 col-md-offset-3">
                        <input type="submit" class="form-control btn-success" id="' . $fieldName . '" name="' . $fieldName . '" value="' . $textOnButton . '" /></div>
                </div>';
        return $string;
    }

    // jQuery Datatable
    public static function jQueryDatatable($tableIDsArray, $columnNumberToSort = 0)
    {
        $string = '';
        if (is_array($tableIDsArray) === true) {
            $string .= '  <script>
                $(function(){';
            foreach ($tableIDsArray as $tableID) {
                $string .= '$("#' . $tableID . '").dataTable({ "iDisplayLength": 50, ' . ($columnNumberToSort > 0 ? ' "order": [[ ' . $columnNumberToSort . ', "asc" ]], ' : '') . ' "responsive": true,  "fixedHeader": true});' . "\n";
            }

            // { "iDisplayLength": 100, "order": [[ ' . $columnNumberToSort . ', "asc" ]], "responsive": true, "scrollX": true, "fixedHeader": true}
            $string .= '}); </script>' . "\n";
        }

        return $string;
    }

    public static function jQueryDatatableSimple($tableIDsArray, $columnNumberToSort = null)
    {
        if ($columnNumberToSort == null) {
            $columnNumberToSort = 1;
        }

        if (is_array($tableIDsArray) === true) {
            $string = '<script>$(function(){ ';
            foreach ($tableIDsArray as $tableID) {
                $string .= '$("#' . $tableID . '").dataTable({ "iDisplayLength": 100, "order": [[ ' . $columnNumberToSort . ', "asc" ]]});' . "\n";
            }

            $string .= '}); </script>' . "\n";
        }

        return $string;
    }

    // javascript functions
    public static function javascript($code)
    {
        return '<script language="javascript" type="text/javascript">' . $code . '</script>' . "\n";
    }

    public static function fileLink($fileName, $url)
    {
        $extension = strtolower(end(explode('.', $fileName)));
        if (in_array($extension, ['doc', 'docx', 'odt']) === true) {
            // word docs
            $faIcon = 'fa-file-word-o';
        } else if (in_array($extension, ['ppt', 'pptx', 'odp']) === true) {
            // ppts
            $faIcon = 'fa-file-powerpoint-o';
        } else if (in_array($extension, ['zip', '7z', 'rar', 'gz', 'tgz']) === true) {
            // zip
            $faIcon = 'fa-file-archive-o';
        } else if (in_array($extension, ['xls', 'xlsx', 'ods']) === true) {
            // excel
            $faIcon = 'fa-file-excel-o';
        } else if (in_array($extension, ['pdf']) === true) {
            // pdf
            $faIcon = 'fa-file-pdf-o';
        } else if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif']) === true) {
            // images
            $faIcon = 'fa-file-image-o';
        } else {
            $faIcon = 'fa-file-o';
        }

        $link = '<a href="' . $url . '" target="_blank"><i class="fa ' . $faIcon . '"></i> ' . $fileName . '</a>';
        return $link;
    }
}
