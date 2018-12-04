<?defined('SYSPATH') OR die('No direct script access.');?>
<?php
/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 29.09.2016
 * Time: 17:17
 */
?>
<div id="main-menu">
    <div>
        <a href="<?=URL::site('companies/create/')?>">New Company</a>
        <ul>
            <li><a href="<?=URL::site('companies')?>">companies</a></li>
            <li><a href="">projects</a></li>
            <li><a href="">forms</a></li>
            <li><a href="">reports</a></li>
            <li><a href="">settings</a></li>
            <li><a href="">users</a></li>
            <li><a href="">preferences</a></li>
            <li><a href="">archive</a></li>
            <li><a href="<?=URL::site('logout')?>">logout</a></li>
        </ul>
    </div>
</div>
<style>
    #main-menu{
        background-color: #83cbfd;
    }
    #main-menu li{
        margin: 0 10px;
        display: inline-block;
    }
    #main-menu div{
        margin: auto;
        width: 800px;
    }
</style>
