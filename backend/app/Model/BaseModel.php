<?php
/**
 * Created by PhpStorm.
 * User: Zdenda
 * Date: 22.4.2018
 * Time: 20:05
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Zakladni trida pro vsechny modely pouzite v projektu.
 * Obsahuje konfiguraci spolecnou pro vsechny modely (zatim jen $timestamps).
 *
 * @package App\Model
 */
class BaseModel extends Model
{
    /**
     * Kvuli ORM frameworku, aby nepredpokladal existenci sloupcu
     */
    public $timestamps = false;
}