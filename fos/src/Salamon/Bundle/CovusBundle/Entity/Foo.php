<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 9/29/14
 * Time: 2:26 AM
 */

namespace Salamon\Bundle\CovusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="covus_foo")
 * @ORM\HasLifecycleCallbacks()
 */
class Foo
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\OneToOne(targetEntity="User")
     */
    protected $user_id;

    /**
     * @var \Salamon\Bundle\CovusBundle\Entity\User $user
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    /**
     * @var boolean $val
     * @ORM\Column(name="val1", type="boolean", nullable=true)
     */
    protected $val1;

    /**
     * @var boolean $va2
     * @ORM\Column(name="val2", type="boolean", nullable=true)
     */
    protected $val2;

    /**
     * @var boolean $va3
     * @ORM\Column(name="val3", type="boolean", nullable=true)
     */
    protected $val3;

    /**
     * @var boolean $va4
     * @ORM\Column(name="val4", type="boolean", nullable=true)
     */
    protected $val4;

    /**
     * @var boolean $va5
     * @ORM\Column(name="val5", type="boolean", nullable=true)
     */
    protected $val5;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime('now');
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime('now');
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param boolean $val1
     */
    public function setVal1($val1)
    {
        $this->val1 = $val1;
    }

    /**
     * @return boolean
     */
    public function getVal1()
    {
        return $this->val1;
    }

    /**
     * @param boolean $val2
     */
    public function setVal2($val2)
    {
        $this->val2 = $val2;
    }

    /**
     * @return boolean
     */
    public function getVal2()
    {
        return $this->val2;
    }

    /**
     * @param boolean $val3
     */
    public function setVal3($val3)
    {
        $this->val3 = $val3;
    }

    /**
     * @return boolean
     */
    public function getVal3()
    {
        return $this->val3;
    }

    /**
     * @param boolean $val4
     */
    public function setVal4($val4)
    {
        $this->val4 = $val4;
    }

    /**
     * @return boolean
     */
    public function getVal4()
    {
        return $this->val4;
    }

    /**
     * @param boolean $val5
     */
    public function setVal5($val5)
    {
        $this->val5 = $val5;
    }

    /**
     * @return boolean
     */
    public function getVal5()
    {
        return $this->val5;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }
}