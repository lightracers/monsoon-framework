<?php

namespace Framework;

use Framework\BootstrapUI;

class Datagrid
{

    /** @var object $getHtml */
    protected $getHtml;

    /** @var object $noDataMessage  */
    protected $noDataMessage;

    /** @var object $source     source for this array */
    public $source;

    /** @var object $colWidths array of widths for each cols */
    protected $colWidths;

    /** @var object $colMapping  array of columns whose content needs to be hyperlinked */
    protected $colMapping;

    /** @var object $stripSlashes  strip slashes in cell data displayed */
    protected $stripSlashes;

    /** @var string $style */
    protected $style;

    /** @var string $headerStyle */
    protected $headerStyle;

    /** @var string $trClass */
    protected $trClass;

    /** @var string $tdClass */
    protected $tdClass;

    /** @var string $width */
    protected $width;

    /** @var string $border */
    protected $border;

    /** @var string $padding */
    protected $padding;

    /** @var string $spacing */
    protected $spacing;

    /** @var string $borderColor */
    protected $borderColor;

    /** @var array $hiddenColumns */
    protected $hiddenColumns = [];

    /** @var string $class */
    protected $class;

    /** @var string $alias */
    protected $alias;

    /** @var string $colWid */
    protected $colWid;

    /** @var string $alternativeColors */
    protected $alternativeColors;

    /** @var string $columnWidths */
    protected $columnWidths;

    /** @var string  */
    private $tableCssClass;

    /** @var string */
    private $html;

    public function __construct()
    {
        $this->tableCssClass = 'table table-hover table-responsive-sm table-sm';
        $this->noDataMessage =  BootstrapUI::alertInfo('No records to display', false);

        $this->source            = new \stdClass();
        $this->source->indexRows = true;
        $this->source->rows      = [];
        $this->source->fields    = [];
        $this->source->rowCount  = 0;
        $this->source->colCount  = 0;
        $this->preserveColumnNames = false;
// if false, _ chars in col names will be replaced with spaces.
    }

    private function validate()
    {
        if (is_object($this->source) == false) {
            $this->source = new \stdClass();
        }

        // set count
        $this->source->rowCount = count($this->source->rows);
        $this->source->colCount = count($this->source->fields);

        $extras = '';

        // if no source

        if ((is_object($this->source) == false || $this->source->rowCount == 0) and $this->getHtml == false) {
            if ($this->noDataMessage == '') {
                $this->html = 'No data/source is assigned to this datagrid';
            } else {
                $this->html = '<p>' . $this->noDataMessage . '</p>';
            }

            return false;
        }

        // default style
        if (empty($this->style) == true && $this->tableCssClass == '') {
            $this->style = "font-family:Arial; font-size:12px";
        }

        // other style information
        $this->border  = empty($this->border) != true ? $this->border : 0;
        $this->padding = empty($this->padding) != true ? $this->padding : 4;
        $this->spacing = empty($this->spacing) != true ? $this->spacing : 0;

        $extras .= ' border="' . $this->border . '"';
        $extras .= ' cellpadding="' . $this->padding . '"';
        $extras .= ' cellspacing="' . $this->spacing . '"';
        if ($this->borderColor != '') {
            $extras .= ' bordercolor="' . $this->borderColor . '"';
        }

        if ($this->width != '') {
            $this->width = '100%';
        }

        $extras .= ' width="' . $this->width . '"';

        // $this->staticStyles=$this->border. $this->padding. $this->spacing. $this->width. $this->borderColor;
        $extras .= ' style="' . $this->style . '"';

        if ($this->tableCssClass != '') {
            $extras .= ' class="' . $this->tableCssClass . '"';
        }

        $this->_staticStyles = $extras;

        if ($this->headerStyle != '' && $this->tableCssClass == '') {
            $this->headerStyle = "background-color:#DDDDDD;";
        }

        return true;
    }

    public function render()
    {
        // Variable initialization
        $colWid     = '';
        $acBgColor  = '';
        $nl         = "\n";
        $this->html = '';

        // validate styles and other info before rendering
        if ($this->validate() == false) {
            if ($this->getHtml == true) {
                return $this->html;
            } else {
                echo $this->html;
            }

            return false;
        }

        // start the table
        $this->html .= '<table ' . $this->_staticStyles . ' name="' . $this->_tableName . '" id="' . $this->_tableName . '">' . $nl;

        // display headers and col headings
        $this->html .= '<thead>' . $nl . '	<tr style="' . $this->headerStyle . '">' . $nl;
        for ($i = 0; $i < $this->source->colCount; $i++) {
            // if column has to be hidden
            if (in_array($this->source->fields[$i], $this->hiddenColumns) == true) {
                continue;
            }

            if ($this->columnWidths[$i] != '') {
                $colWid = ' width="' . $this->columnWidths[$i] . '"';
            }

            // if there is any columnalias
            if (empty($this->alias[$this->source->fields[$i]]) != true) {
                $colName = $this->alias[$this->source->fields[$i]];
            } else {
                $colName = $this->source->fields[$i];
            }

            // format column names if required

            if ($this->preserveColumnNames == false) {
                $colName = ucwords(str_replace(['_'], ' ', $this->source->fields[$i]));
            } else {
                $colName = ucfirst($colName);
            }

            $this->html .= '		<th' . $colWid . '><strong>' . $colName . '</strong></th>' . $nl;
            $colName     = '';
        }

        $this->html .= '	</tr></thead>' . $nl;

        // flip fields for colmapping keys, if required.
        $flippedFields = [];
        if (is_array($this->colMapping) == true && count($this->colMapping) > 0) {
            $flippedFields = array_flip($this->source->fields);
        }

        $this->html .= '<tbody>' . $nl;
        for ($i = 0; $i < $this->source->rowCount; $i++) {
            // if to skip first row
            if ($i == 0 && isset($this->skipFirstRow) == true) {
                continue;
            }

            if ($this->alternativeColors != '') {
                $acBgColor = ' bgcolor="' . $this->alternativeColors[($i % 2)] . '"';
            }

            $this->html .= '	<tr' . $this->trClass . $acBgColor . '>' . $nl;
            for ($j = 0; $j < $this->source->colCount; $j++) {
                // if this column is hidden
                if (in_array($this->source->fields[$j], $this->hiddenColumns) == true) {
                    continue;
                }

                // if this col needs to be linked
                if ($this->colMapping[$this->source->fields[$j]] != '' && $this->getHtml != true) {
                    // display linked col info
                    $urlMap    = $this->colMapping[$this->source->fields[$j]];
                    $mapParts  = explode('<COL:', $urlMap);
                    $urlString = '';
                    for ($k = 1; $k < count($mapParts); $k++) {
                        $col = explode('>', $mapParts[$k]);
                        // explode the column name
                        if (isset($col) == true && $col[0] != '') {
                            if (isset($this->source->indexRows) == true) {
                                $colValue = $this->source->rows[$i][$col[0]];
                            } else {
                                $colValue = $this->source->rows[$i][$flippedFields[$col[0]]];
                            }

                            array_shift($col);
                            $urlString .= $colValue . join('', $col);
                        }
                    }

                    $urlString = $mapParts[0] . $urlString;

                    $this->html .= '		<td' . $this->tdClass . '><a href="' . $urlString . '"';
                    if (isset($this->colMappingProperties) == true && $this->colMappingProperties[$this->source->fields[$j]]['clickConfirmationMessage'] != '') {
                        $this->html .= ' onclick="return confirm(\'' . $this->colMappingProperties[$this->source->fields[$j]]['clickConfirmationMessage'] . '\')" ';
                    }

                    $this->html .= ' >';

                    if ($this->source->indexRows == true) {
                        $cellContent = $this->source->rows[$i][$this->source->fields[$j]];
                    } else {
                        $cellContent = $this->source->rows[$i][$j];
                    }

                    // strip slashes if asked for
                    $cellContent = $this->stripSlashes == true ? stripslashes($cellContent) : $cellContent;

                    $this->html .= $cellContent;
                    $this->html .= '</a></td>' . $nl;

                    unset($urlString, $mapParts);
                } else {
                    // display normal table, without including colMapping
                    $this->html .= '		<td>';
                    if ($this->source->indexRows == true) {
                        $this->html .= $this->source->rows[$i][$this->source->fields[$j]];
                    } else {
                        $this->html .= $this->source->rows[$i][$j];
                    }

                    $this->html .= '</td>' . $nl;
                }
            }

            $this->html .= '	</tr>' . $nl;
        }

        $this->html .= '</tbody>' . $nl;
        // end table
        $this->html .= '<tfoot></tfoot></table>';
        if ($this->getHtml == true) {
            $this->getHtml = false;
            return $this->html;
        } else {
            echo $this->html;
        }

        return null;
    }

    public function getHtml()
    {
        $this->getHtml = true;
        return $this->render();
    }

    public function setColumnAlias($columnName, $displayName)
    {
        $this->alias[$columnName] = $displayName;
    }

    public function hideColumn($columnName)
    {
        $this->hiddenColumns[] = $columnName;
    }

    public function hideColumns()
    {
        if (is_array(func_get_arg(0)) == true) {
            foreach (func_get_arg(0) as $colName) {
                $this->hideColumn($colName);
            }
        } else {
            for ($i = 0; $i < func_num_args(); $i++) {
                $this->hideColumn(func_get_arg($i));
            }
        }
    }

    public function addColumn($columnName, $value = '')
    {
        // add the column to the list of fields
        $this->source->fields[] = $columnName;

        // if the $value!='', then add column values to the row
        if ($this->source->rowCount > 0 && $value != '') {
            if ($this->source->indexRows == true) {
                for ($i = 0; $i < $this->source->rowCount; $i++) {
                    $this->source->rows[$i][$columnName] = $value;
                }
            } else {
                for ($i = 0; $i < $this->source->rowCount; $i++) {
                    $this->source->rows[$i][] = $value;
                }
            }
        }
    }

    public function setColumnLinking($columnName, $linkpattern, $onClickConfirmationMessage = '')
    {
        if ($onClickConfirmationMessage != '') {
            $this->colMappingProperties[$columnName]['clickConfirmationMessage'] = $onClickConfirmationMessage;
        }

        $this->colMapping[$columnName] = $linkpattern;
    }

    public function addRow(array $rowArray)
    {
        // if array is passed, add it as it is

        if (is_array($rowArray) == true) {
            $this->source->rows[] = $rowArray;
            $this->source->fields = array_keys($rowArray);
        } else {
            // else if arguments are passed, add all args as a single row
            $this->source->rows[] = func_get_args();
        }

        $this->source->rowCount ++;
    }

    public function setRowAlternativeColors($color1, $color2)
    {
        $this->alternativeColors = [
            $color1,
            $color2,
        ];
    }

    public function setTableCssClass($cssClassName)
    {
        $this->tableCssClass = $cssClassName;
    }

    public function setNoDataMessage($message)
    {
        $this->noDataMessage = $message;
    }

    public function getNoDataMessage()
    {
        return $this->noDataMessage;
    }

    public function setDataSource($source)
    {
        $this->source = $source;
    }

    public function setSkipFirstRow($bool = false)
    {
        $this->skipFirstRow = $bool;
    }

    public function setColumnWidths($widthsArray)
    {
        $this->columnWidths = $widthsArray;
    }

    public function setTableName($tableName)
    {
        $this->_tableName = $tableName;
    }

    public function preserveColumnNames($boolValue = false)
    {
        $this->preserveColumnNames = $boolValue;
    }

    public function clear()
    {
        unset($this->source);
        $this->source = new \stdClass();
    }

    /**
     * reset() - alias of clear();
     */
    public function reset()
    {
        $this->clear();
    }

    //

    /**
     * Returns paging links like 1 - 2 - [3] - 4 - 5
     *
     * @param string $pageToLink
     *        	Denote with a caret e.g./Mypage/page/^/id/23
     * @param string $displayText
     *        	Denote page number with caret e.g. Page - ^
     * @param int $totalRecCount
     * @param int $perPageCount
     * @param int $highlightPage
     *        	page number to be highlighted
     * @param bool $useDefaultLayout
     *        	if true plain text, if false, colored boxes
     * @return string
     */
    public static function generatePagingLinks($pageToLink, $displayText, $totalRecCount, $perPageCount = 20, $highlightPage = 1, $useDefaultLayout = true)
    {
        $links = [];
        if ($displayText == null) {
            $displayText = ' ^ ';
        }

        // Calculate how many pages are required
        $pageCount = ceil($totalRecCount / $perPageCount);

        if ($useDefaultLayout == true) {
            // replace carets with page numbers and create links.

            for ($i = 0; $i < $pageCount; $i++) {
                if ($i == $highlightPage) {
                    $links[] = ' [ ' . str_replace('^', ($i + 1), $displayText) . ' ] ';
                } else {
                    $links[] = '<a href="' . str_replace('^', ($i + 1), $pageToLink) . '">' . str_replace('^', ($i + 1), $displayText) . '</a>';
                }
            }

            $pagingText  = '<p>';
            $pagingText .= 'Pages: ' . join(' - ', $links);
            $pagingText .= '</p>';
        } else {
            $highlightStyle = 'padding:5px 8px 5px 8px;border:1px solid #d9d9d9;background:orange;color:black;font-weight:bold;';
            $normalStyle    = 'padding:3px 5px 3px 5px;border:1px solid #d9d9d9;background:#d9d9d9;color:black;';

            for ($i = 0; $i < $pageCount; $i++) {
                if ($i == $highlightPage) {
                    $links[] = '<span style="' . $highlightStyle . '"> ' . str_replace('^', ($i + 1), $displayText) . ' </span>';
                } else {
                    $links[] = '<span style="' . $normalStyle . '"><a href="' . str_replace('^', ($i + 1), $pageToLink) . '">' . str_replace('^', ($i + 1), $displayText) . '</a></span>';
                }
            }

            $pagingText  = '<p><span style="' . $normalStyle . '">&nbsp;</span>';
            $pagingText .= '' . join('', $links);
            $pagingText .= '<span style="' . $normalStyle . '">&nbsp;</span></p>';
        }

        /*
         * normal
         * padding:3px 5px 3px 5px;border:1px solid #d9d9d9;background:#d9d9d9;color:black;
         *
         * highlighted
         * padding:5px 8px 5px 8px;border:1px solid #d9d9d9;background:orange;color:black;font-weight:bold;
         */

        return $pagingText;
    }

}
