<?php
/**
 * Wscn
 *
 * @link      https://github.com/wallstreetcn/wallstreetcn
 * @copyright Copyright (c) 2010-2014 WallstreetCN
 * @author    WallstreetCN Team: shao<liujaysen@gmail.com>
 */
namespace Eva\EvaUser\Entities;


interface CommentUser
{
    public function getId();
    public function getName();
    public function getAvatar();
    public function getUserType();
}