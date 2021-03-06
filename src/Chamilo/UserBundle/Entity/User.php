<?php
/* For licensing terms, see /license.txt */

namespace Chamilo\UserBundle\Entity;

//use Chamilo\CoreBundle\Entity\UserFieldValues;
use Chamilo\CoreBundle\Entity\AccessUrl;
use Chamilo\CoreBundle\Entity\AccessUrlRelUser;
use Chamilo\CoreBundle\Entity\ExtraFieldValues;
use Chamilo\CoreBundle\Entity\UsergroupRelUser;
use Chamilo\CoreBundle\Entity\Skill;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser as BaseUser;
use Sylius\Component\Attribute\Model\AttributeSubjectInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Doctrine\Common\Collections\Collection;

use Chamilo\ThemeBundle\Model\UserInterface as ThemeUser;

//use Chamilo\CoreBundle\Component\Auth;
//use FOS\MessageBundle\Model\ParticipantInterface;
//use Chamilo\ThemeBundle\Model\UserInterface as ThemeUser;
//use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Chamilo\MediaBundle\Entity\Media;
//use Chamilo\UserBundle\Model\UserInterface as UserInterfaceModel;

//use Sylius\Component\Attribute\Model\AttributeValueInterface as BaseAttributeValueInterface;
//use Sylius\Component\Variation\Model\OptionInterface as BaseOptionInterface;
//use Sylius\Component\Variation\Model\VariantInterface as BaseVariantInterface;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(
 *  name="user",
 *  indexes={
 *      @ORM\Index(name="idx_user_uid", columns={"user_id"}),
 *      @ORM\Index(name="status", columns={"status"})
 *  }
 * )
 * //Vich\Uploadable
 * @UniqueEntity("username")
 * @ORM\Entity(repositoryClass="Chamilo\UserBundle\Repository\UserRepository")
 * @ORM\EntityListeners({"Chamilo\UserBundle\Entity\Listener\UserListener"})
 *
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="email",
 *         column=@ORM\Column(
 *             name="email",
 *             type="string",
 *             length=255,
 *             unique=false
 *         )
 *     ),
 *     @ORM\AttributeOverride(name="emailCanonical",
 *         column=@ORM\Column(
 *             name="emailCanonical",
 *             type="string",
 *             length=255,
 *             unique=false
 *         )
 *     )
 * })
 *
 */
class User extends BaseUser implements ThemeUser
{
    const COURSE_MANAGER = 1;
    const TEACHER = 1;
    const SESSION_ADMIN = 3;
    const DRH = 4;
    const STUDENT = 5;
    const ANONYMOUS = 6;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    protected $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=100, nullable=false, unique=true)
     */
    //protected $username;

    /**
     * @var string
     *
     * * @ORM\Column(name="username_canonical", type="string", length=100, nullable=false, unique=true)
     */
    //protected $usernameCanonical;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=false, unique=false)
     */
    //protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=60, nullable=true, unique=false)
     */
    //protected $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=60, nullable=true, unique=false)
     */
    //protected $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false, unique=false)
     */
    //protected $password;

    /**
     * @var string
     *
     * @ORM\Column(name="auth_source", type="string", length=50, nullable=true, unique=false)
     */
    private $authSource;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="official_code", type="string", length=40, nullable=true, unique=false)
     */
    private $officialCode;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=30, nullable=true, unique=false)
     */
    //protected $phone;

    /**
     * @var string
     * @ORM\Column(name="picture_uri", type="string", length=250, nullable=true, unique=false)
     */
    private $pictureUri;

    /**
     * Media type
     * @ORM\ManyToOne(targetEntity="Chamilo\MediaBundle\Entity\Media", cascade={"all"} )
     * @ORM\JoinColumn(name="picture", referencedColumnName="id")
     */
    protected $picture;

    /**
     * @var integer
     *
     * @ORM\Column(name="creator_id", type="integer", nullable=true, unique=false)
     */
    private $creatorId;

    /**
     * @var string
     *
     * @ORM\Column(name="competences", type="text", nullable=true, unique=false)
     */
    private $competences;

    /**
     * @var string
     *
     * @ORM\Column(name="diplomas", type="text", nullable=true, unique=false)
     */
    private $diplomas;

    /**
     * @var string
     *
     * @ORM\Column(name="openarea", type="text", nullable=true, unique=false)
     */
    private $openarea;

    /**
     * @var string
     *
     * @ORM\Column(name="teach", type="text", nullable=true, unique=false)
     */
    private $teach;

    /**
     * @var string
     *
     * @ORM\Column(name="productions", type="string", length=250, nullable=true, unique=false)
     */
    private $productions;

    /**
     * @var integer
     *
     * @ORM\Column(name="chatcall_user_id", type="integer", nullable=true, unique=false)
     */
    private $chatcallUserId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="chatcall_date", type="datetime", nullable=true, unique=false)
     */
    private $chatcallDate;

    /**
     * @var string
     *
     * @ORM\Column(name="chatcall_text", type="string", length=50, nullable=true, unique=false)
     */
    private $chatcallText;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=40, nullable=true, unique=false)
     */
    private $language;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registration_date", type="datetime", nullable=false, unique=false)
     */
    private $registrationDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expiration_date", type="datetime", nullable=true, unique=false)
     */
    private $expirationDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false, unique=false)
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="openid", type="string", length=255, nullable=true, unique=false)
     */
    private $openid;

    /**
     * @var string
     *
     * @ORM\Column(name="theme", type="string", length=255, nullable=true, unique=false)
     */
    private $theme;

    /**
     * @var integer
     *
     * @ORM\Column(name="hr_dept_id", type="smallint", nullable=true, unique=false)
     */
    private $hrDeptId;

    /**
     * @var AccessUrl
     **/
    protected $currentUrl;

    /**
     * @ORM\Column(type="string", length=255)
     */
    //protected $salt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=true, unique=false)
     */
    //protected $lastLogin;

    /**
     * Random string sent to the user email address in order to verify it
     *
     * @var string
     * @ORM\Column(name="confirmation_token", type="string", length=255, nullable=true)
     */
    //protected $confirmationToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="password_requested_at", type="datetime", nullable=true, unique=false)
     */
    //protected $passwordRequestedAt;

    /**
     * @ORM\OneToMany(targetEntity="Chamilo\CoreBundle\Entity\CourseRelUser", mappedBy="user")
     **/
    protected $courses;

    /**
     * @ORM\OneToMany(targetEntity="Chamilo\CourseBundle\Entity\CItemProperty", mappedBy="user")
     **/
    //protected $items;

    /**
     * @ORM\OneToMany(targetEntity="Chamilo\CoreBundle\Entity\UsergroupRelUser", mappedBy="user")
     **/
    protected $classes;

    /**
     * ORM\OneToMany(targetEntity="Chamilo\CourseBundle\Entity\CDropboxPost", mappedBy="user")
     **/
    protected $dropBoxReceivedFiles;

    /**
     * ORM\OneToMany(targetEntity="Chamilo\CourseBundle\Entity\CDropboxFile", mappedBy="userSent")
     **/
    protected $dropBoxSentFiles;

    /**
     * @ORM\OneToMany(targetEntity="Chamilo\CoreBundle\Entity\JuryMembers", mappedBy="user")
     **/
    //protected $jurySubscriptions;

    /**
     * @ORM\ManyToMany(targetEntity="Chamilo\UserBundle\Entity\Group")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    //private $isActive;

    /**
     * ORM\OneToMany(targetEntity="Chamilo\CoreBundle\Entity\CurriculumItemRelUser", mappedBy="user")
     **/
    protected $curriculumItems;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Chamilo\CoreBundle\Entity\AccessUrlRelUser", mappedBy="user", cascade={"persist"}, orphanRemoval=true)
     */
    protected $portals;

    /**
     * @ORM\OneToMany(targetEntity="Chamilo\CoreBundle\Entity\Session", mappedBy="generalCoach")
     **/
    protected $sessionAsGeneralCoach;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(
     *     targetEntity="Chamilo\CoreBundle\Entity\ExtraFieldValues",
     *     mappedBy="user",
     *     orphanRemoval=true, cascade={"persist"}
     * )
     **/
    protected $extraFieldValues;

    /**
     * ORM\OneToMany(targetEntity="Chamilo\CoreBundle\Entity\Resource\ResourceNode", mappedBy="creator")
     **/
    protected $resourceNodes;

    /**
     * @ORM\OneToMany(targetEntity="Chamilo\CoreBundle\Entity\SessionRelCourseRelUser", mappedBy="user", cascade={"persist"})
     **/
    protected $sessionCourseSubscriptions;

    /**
     * @ORM\OneToMany(targetEntity="Chamilo\CoreBundle\Entity\SkillRelUser", mappedBy="user", cascade={"persist"})
     */
    protected $achievedSkills;

    /**
     * @ORM\OneToMany(targetEntity="Chamilo\CoreBundle\Entity\SkillRelUserComment", mappedBy="feedbackGiver")
     */
    protected $commentedUserSkills;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->status = self::STUDENT;
        parent::__construct();

        $this->salt = sha1(uniqid(null, true));
        $this->isActive = true;
        $this->active = 1;
        $this->authSource = 'platform';
        $this->courses = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->classes = new ArrayCollection();
        //$this->roles = new ArrayCollection();
        $this->curriculumItems = new ArrayCollection();
        $this->portals = new ArrayCollection();
        $this->dropBoxSentFiles = new ArrayCollection();
        $this->dropBoxReceivedFiles = new ArrayCollection();
        $this->chatcallUserId = 0;
        $this->extraFieldValues = new ArrayCollection();
        $this->userId = 0;

        $this->registrationDate = new \DateTime();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getCompleteName();
    }

    /**
     * Updates the id with the user_id
     *  @ORM\PostPersist()
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        //parent::postPersist();
        // Updates the user_id field
        $user = $args->getEntity();
        $this->setUserId($user->getId());
        /*$em = $args->getEntityManager();
        $em->persist($user);
        $em->flush();*/
    }

    /**
     * @param int $userId
     */
    public function setId($userId)
    {
        $this->id = $userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        if (!empty($userId)) {
            $this->userId = $userId;
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEncoderName()
    {
        return "legacy_encoder";
    }

    /**
     * @return ArrayCollection
     */
    public function getDropBoxSentFiles()
    {
        return $this->dropBoxSentFiles;
    }

    /**
     * @return ArrayCollection
     */
    public function getDropBoxReceivedFiles()
    {
        return $this->dropBoxReceivedFiles;
    }

    /**
     * @return ArrayCollection
     */
    public function getCourses()
    {
        return $this->courses;
    }

    /**
     * @return array
     */
    public static function getPasswordConstraints()
    {
        return
            array(
                new Assert\Length(array('min' => 5)),
                // Alpha numeric + "_" or "-"
                new Assert\Regex(array(
                        'pattern' => '/^[a-z\-_0-9]+$/i',
                        'htmlPattern' => '/^[a-z\-_0-9]+$/i')
                ),
                // Min 3 letters - not needed
                /*new Assert\Regex(array(
                    'pattern' => '/[a-z]{3}/i',
                    'htmlPattern' => '/[a-z]{3}/i')
                ),*/
                // Min 2 numbers
                new Assert\Regex(array(
                        'pattern' => '/[0-9]{2}/',
                        'htmlPattern' => '/[0-9]{2}/')
                ),
            )
            ;
    }

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        //$metadata->addPropertyConstraint('firstname', new Assert\NotBlank());
        //$metadata->addPropertyConstraint('lastname', new Assert\NotBlank());
        //$metadata->addPropertyConstraint('email', new Assert\Email());
        /*
        $metadata->addPropertyConstraint('password',
            new Assert\Collection(self::getPasswordConstraints())
        );*/

        /*$metadata->addConstraint(new UniqueEntity(array(
            'fields'  => 'username',
            'message' => 'This value is already used.',
        )));*/

        /*$metadata->addPropertyConstraint(
            'username',
            new Assert\Length(array(
                'min'        => 2,
                'max'        => 50,
                'minMessage' => 'This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.',
                'maxMessage' => 'This value is too long. It should have {{ limit }} character or less.|This value is too long. It should have {{ limit }} characters or less.',
            ))
        );*/
    }

    /**
     * @inheritDoc
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        /*if ($this->password !== $user->getPassword()) {
            return false;
        }*/

        /*if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }*/

        /*if ($this->username !== $user->getUsername()) {
            return false;
        }*/

        return true;
    }

    /**
     * @return ArrayCollection
     */
    public function getPortals()
    {
        return $this->portals;
    }

    /**
     * @param $portal
     */
    public function setPortal($portal)
    {
        $this->portals->add($portal);
    }

    /**
     * @return ArrayCollection
     */
    public function getCurriculumItems()
    {
        return $this->curriculumItems;
    }

    /**
     * @param $items
     */
    public function setCurriculumItems($items)
    {
        $this->curriculumItems = $items;
    }

    /**
     * @return bool
     */
    public function getIsActive()
    {
        return $this->active == 1;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->getIsActive();
    }

    /**
     * @inheritDoc
     */
    public function isAccountNonExpired()
    {
        return true;
        /*$now = new \DateTime();
        return $this->getExpirationDate() < $now;*/
    }

    /**
     * @inheritDoc
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled()
    {
        return $this->getActive() == 1;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

    /**
     *
     * @return ArrayCollection
     */
    /*public function getRolesObj()
    {
        return $this->roles;
    }*/

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @return ArrayCollection
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     *
     */
    public function getLps()
    {
        //return $this->lps;
        /*$criteria = Criteria::create()
            ->where(Criteria::expr()->eq("id", "666"))
            //->orderBy(array("username" => "ASC"))
            //->setFirstResult(0)
            //->setMaxResults(20)
        ;
        $lps = $this->lps->matching($criteria);*/
        /*return $this->lps->filter(
            function($entry) use ($idsToFilter) {
                return $entry->getId() == 1;
        });*/
    }

    /**
     * @todo don't use api_get_person_name
     * @return string
     */
    public function getCompleteName()
    {
        return api_get_person_name($this->firstname, $this->lastname);
    }

    /**
     * Returns the list of classes for the user
     * @return string
     */
    public function getCompleteNameWithClasses()
    {
        $classSubscription = $this->getClasses();
        $classList = array();
        /** @var UsergroupRelUser $subscription */
        foreach ($classSubscription as $subscription) {
            $class = $subscription->getUsergroup();
            $classList[] = $class->getName();
        }
        $classString = !empty($classList) ? ' ['.implode(', ', $classList).']' : null;

        return $this->getCompleteName().$classString;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set authSource
     *
     * @param string $authSource
     * @return User
     */
    public function setAuthSource($authSource)
    {
        $this->authSource = $authSource;

        return $this;
    }

    /**
     * Get authSource
     *
     * @return string
     */
    public function getAuthSource()
    {
        return $this->authSource;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set status
     *
     * @param int $status
     *
     * @return User
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set officialCode
     *
     * @param string $officialCode
     * @return User
     */
    public function setOfficialCode($officialCode)
    {
        $this->officialCode = $officialCode;

        return $this;
    }

    /**
     * Get officialCode
     *
     * @return string
     */
    public function getOfficialCode()
    {
        return $this->officialCode;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set pictureUri
     *
     * @param string $pictureUri
     * @return User
     */
    public function setPictureUri($pictureUri)
    {
        $this->pictureUri = $pictureUri;

        return $this;
    }

    /**
     * Get pictureUri
     *
     * @return string
     */
    public function getPictureUri()
    {
        return $this->pictureUri;
    }

    /**
     * Set creatorId
     *
     * @param integer $creatorId
     * @return User
     */
    public function setCreatorId($creatorId)
    {
        $this->creatorId = $creatorId;

        return $this;
    }

    /**
     * Get creatorId
     *
     * @return integer
     */
    public function getCreatorId()
    {
        return $this->creatorId;
    }

    /**
     * Set competences
     *
     * @param string $competences
     * @return User
     */
    public function setCompetences($competences)
    {
        $this->competences = $competences;

        return $this;
    }

    /**
     * Get competences
     *
     * @return string
     */
    public function getCompetences()
    {
        return $this->competences;
    }

    /**
     * Set diplomas
     *
     * @param string $diplomas
     * @return User
     */
    public function setDiplomas($diplomas)
    {
        $this->diplomas = $diplomas;

        return $this;
    }

    /**
     * Get diplomas
     *
     * @return string
     */
    public function getDiplomas()
    {
        return $this->diplomas;
    }

    /**
     * Set openarea
     *
     * @param string $openarea
     * @return User
     */
    public function setOpenarea($openarea)
    {
        $this->openarea = $openarea;

        return $this;
    }

    /**
     * Get openarea
     *
     * @return string
     */
    public function getOpenarea()
    {
        return $this->openarea;
    }

    /**
     * Set teach
     *
     * @param string $teach
     * @return User
     */
    public function setTeach($teach)
    {
        $this->teach = $teach;

        return $this;
    }

    /**
     * Get teach
     *
     * @return string
     */
    public function getTeach()
    {
        return $this->teach;
    }

    /**
     * Set productions
     *
     * @param string $productions
     * @return User
     */
    public function setProductions($productions)
    {
        $this->productions = $productions;

        return $this;
    }

    /**
     * Get productions
     *
     * @return string
     */
    public function getProductions()
    {
        return $this->productions;
    }

    /**
     * Set chatcallUserId
     *
     * @param integer $chatcallUserId
     * @return User
     */
    public function setChatcallUserId($chatcallUserId)
    {
        $this->chatcallUserId = $chatcallUserId;

        return $this;
    }

    /**
     * Get chatcallUserId
     *
     * @return integer
     */
    public function getChatcallUserId()
    {
        return $this->chatcallUserId;
    }

    /**
     * Set chatcallDate
     *
     * @param \DateTime $chatcallDate
     * @return User
     */
    public function setChatcallDate($chatcallDate)
    {
        $this->chatcallDate = $chatcallDate;

        return $this;
    }

    /**
     * Get chatcallDate
     *
     * @return \DateTime
     */
    public function getChatcallDate()
    {
        return $this->chatcallDate;
    }

    /**
     * Set chatcallText
     *
     * @param string $chatcallText
     * @return User
     */
    public function setChatcallText($chatcallText)
    {
        $this->chatcallText = $chatcallText;

        return $this;
    }

    /**
     * Get chatcallText
     *
     * @return string
     */
    public function getChatcallText()
    {
        return $this->chatcallText;
    }

    /**
     * Set language
     *
     * @param string $language
     * @return User
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set registrationDate
     *
     * @param \DateTime $registrationDate
     * @return User
     */
    public function setRegistrationDate($registrationDate)
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    /**
     * Get registrationDate
     *
     * @return \DateTime
     */
    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    /**
     * Set expirationDate
     *
     * @param \DateTime $expirationDate
     *
     * @return User
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    /**
     * Get expirationDate
     *
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return User
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set openid
     *
     * @param string $openid
     * @return User
     */
    public function setOpenid($openid)
    {
        $this->openid = $openid;

        return $this;
    }

    /**
     * Get openid
     *
     * @return string
     */
    public function getOpenid()
    {
        return $this->openid;
    }

    /**
     * Set theme
     *
     * @param string $theme
     * @return User
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get theme
     *
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Set hrDeptId
     *
     * @param integer $hrDeptId
     * @return User
     */
    public function setHrDeptId($hrDeptId)
    {
        $this->hrDeptId = $hrDeptId;

        return $this;
    }

    /**
     * Get hrDeptId
     *
     * @return integer
     */
    public function getHrDeptId()
    {
        return $this->hrDeptId;
    }

    /**
     * @return Media
     */
    public function getAvatar()
    {
        return $this->getPictureUri();
    }

    /**
     * @return string
     */
    public function getAvatarOrAnonymous()
    {
        $avatar = $this->getAvatar();

        if (empty($avatar)) {
            return 'bundles/chamilocore/img/unknown.jpg';
        }

        return $avatar;
    }

    /**
     * @return \DateTime
     */
    public function getMemberSince()
    {
        return $this->registrationDate;
    }

    /**
     * @return bool
     */
    public function isOnline()
    {
        return false;
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return $this->getId();
    }


    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->getUsername();
    }

    /**
     * @param $slug
     * @return User
     */
    public function setSlug($slug)
    {
        return $this->setUsername($slug);
    }

    /**
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     *
     * @return User
     */
    public function setLastLogin(\DateTime $lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtraFieldValues()
    {
        /** @var ExtraFieldValues $extraField */
        /*foreach ($this->extraFields as &$extraField) {
            $extraField->setUser($this);
        }*/

        return $this->extraFieldValues;
    }

    /**
     * {@inheritdoc}
     */
    public function addExtraFieldValue(ExtraFieldValues $extraFieldValue)
    {
        //if (!$this->hasExtraField($attribute)) {
        $extraFieldValue->setUser($this);
        $this->extraFieldValues->add($extraFieldValue);
        //$this->extraFields[] = $extraFieldValue;
        //}

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeExtraFieldValue(ExtraFieldValues $attribute)
    {
        //if ($this->hasExtraField($attribute)) {
        $this->extraFieldValue->removeElement($attribute);
            //$attribute->setUser($this);
        //}

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function hasExtraFieldByName($attributeName)
    {
        foreach ($this->extraFieldValues as $attribute) {
            if ($attribute->getName() === $attributeName) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtraFieldByName($attributeName)
    {
        foreach ($this->extraFieldValues as $attribute) {
            if ($attribute->getName() === $attributeName) {
                return $attribute;
            }
        }

        return null;
    }

    /**
     * Get sessionCourseSubscription
     * @return ArrayCollection
     */
    public function getSessionCourseSubscriptions()
    {
        return $this->sessionCourseSubscriptions;
    }

    /**
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * @param string $confirmationToken
     *
     * @return User
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }


    /**
     * @param int $ttl
     * @return bool
     */
    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
        $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    /**
     * Get achievedSkills
     * @return ArrayCollection
     */
    public function getAchievedSkills()
    {
        return $this->achievedSkills;
    }

    /**
     * Check if the user has the skill
     * @param \Chamilo\CoreBundle\Entity\Skill $skill The skill
     * @return boolean
     */
    public function hasSkill(Skill $skill)
    {
        $achievedSkills = $this->getAchievedSkills();

        foreach ($achievedSkills as $userSkill) {
            if ($userSkill->getSkill()->getId() !== $skill->getId()) {
                continue;
            }

            return true;
        }
    }

    /**
     * @return Media
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * @param mixed $picture
     * @return User
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Sets the AccessUrl for the current user in memory
     * @param AccessUrl $url
     *
     * @return $this
     */
    public function setCurrentUrl(AccessUrl $url)
    {
        $urlList = $this->getPortals();
        /** @var AccessUrlRelUser $item */
        foreach ($urlList as $item) {
            if ($item->getPortal()->getId() == $url->getId()) {
                $this->currentUrl = $url;
                break;
            }
        }

        return $this;
    }

    /**
     * @return AccessUrl
     */
    public function getCurrentUrl()
    {
        return $this->currentUrl;
    }

//    /**
//     * {@inheritdoc}
//     */
//    public function getAttributes()
//    {
//        return $this->extraFieldValues;
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function setAttributes(Collection $attributes)
//    {
//        foreach ($attributes as $attribute) {
//            $this->addAttribute($attribute);
//        }
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function addAttribute(AttributeValueInterface $attribute)
//    {
//        if (!$this->hasAttribute($attribute)) {
//            /** @var ExtraFieldValues $attribute */
//            $attribute->setSubjectUser($this);
//            $this->extraFieldValues[] = $attribute;
//        }
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function removeAttribute(AttributeValueInterface $attribute)
//    {
//        if ($this->hasAttribute($attribute)){
//            $attribute->setSubject(null);
//            $key = array_search($attribute, $this->extraFieldValues->toArray());
//            unset($this->extraFieldValues[$key]);
//        }
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function hasAttribute(AttributeValueInterface $attribute)
//    {
//        return in_array($attribute, $this->extraFieldValues->toArray());
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function hasAttributeByName($attributeName)
//    {
//        foreach ($this->extraFieldValues as $attribute) {
//            if ($attribute->getName() === $attributeName) {
//                return true;
//            }
//        }
//
//        return false;
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function getAttributeByName($attributeName)
//    {
//        foreach ($this->extraFieldValues as $attribute) {
//            if ($attribute->getName() === $attributeName) {
//                return $attribute;
//            }
//        }
//
//        return null;
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function hasAttributeByCode($attributeCode)
//    {
//
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function getAttributeByCode($attributeCode)
//    {
//
//    }

}
