<?php
/**
 * Created by PhpStorm.
 * User: Zdenda
 * Date: 20.4.2018
 * Time: 20:09
 */

namespace App\Http\Controllers;

use App\Model\Device;
use App\Model\Zarizeni;
use App\Model\Zaznam;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Mixed_;

class RangeController extends Controller
{

    /**
     * Vrati časový rozsah záznamů
     *
     * @return Zarizeni
     */
    public function getRange()
    {
        $rangeDate = Zaznam::lastDateAndFirstDate();

        if ($rangeDate != null) {
            return json_encode($rangeDate);
        } else {
            return response('Not found.', 404);
        }
    }

}
