<?php

/**
 * Created by PhpStorm.
 * User: HongYang
 * Date: 2016/12/18
 * Time: 15:23
 */
class BranchMember
{
    public $name = "";
    public $class = "";
    public $birth_date = "";
    public $nation = "";
    public $place_of_origin = "";
    public $state = "";
    public $develop_date = "";
    public $formal_date = "";
    public $type = "";
    public $major = "";
    public $phone = "";
    public $post = "";

    function __construct( $par1, $par2, $par3, $par4, $par5, $par6,
                          $par7, $par8, $par9, $parA, $parB, $parC ) {
        $this->name = $par1;
        $this->class = $par2;
        $this->birth_date = $par3;
        $this->nation = $par4;
        $this->place_of_origin = $par5;
        $this->state = $par6;
        $this->develop_date = $par7;
        $this->formal_date = $par8;
        $this->type = $par9;
        $this->major = $parA;
        $this->phone = $parB;
        $this->post = $parC;
    }
}