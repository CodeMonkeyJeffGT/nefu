<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ScoreItemRepository")
 */
class ScoreItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $account;

    /**
     * @ORM\Column(type="integer")
     */
    private $lesson_id;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $score;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $type;

    public function getId()
    {
        return $this->id;
    }

    public function getAccount(): ?int
    {
        return $this->account;
    }

    public function setAccount(int $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getLessonId(): ?int
    {
        return $this->lesson_id;
    }

    public function setLessonId(int $lesson_id): self
    {
        $this->lesson_id = $lesson_id;

        return $this;
    }

    public function getScore(): ?string
    {
        return $this->score;
    }

    public function setScore(string $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
