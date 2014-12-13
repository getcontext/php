<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 9/29/14
 * Time: 2:26 AM
 */

namespace Salamon\Bundle\CovusBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="covus_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @var Foo
     *
     * @ORM\OneToOne(targetEntity="Foo")
     */
    protected $foo;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param \Salamon\Bundle\CovusBundle\Entity\Foo $foo
     */
    public function setFoo($foo)
    {
        $this->foo = $foo;
    }

    /**
     * @return \Salamon\Bundle\CovusBundle\Entity\Foo
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }



}