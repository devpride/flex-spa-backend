<?php
declare(strict_types=1);

namespace App\Service\Creation\Post;

/**
 * Class PostDto
 */
class PostDto implements \Serializable
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $summary;

    /**
     * @var string
     */
    private $content;

    /**
     * @var \DateTimeImmutable
     */
    private $publishedAt;

    /**
     * @var string[]
     */
    private $tags;

    /**
     * @var int
     */
    private $authorId;

    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return PostDto
     */
    public function setTitle(string $title) : PostDto
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getSummary() : string
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     *
     * @return PostDto
     */
    public function setSummary(string $summary) : PostDto
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent() : string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return PostDto
     */
    public function setContent(string $content) : PostDto
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getPublishedAt() : \DateTimeImmutable
    {
        return $this->publishedAt;
    }

    /**
     * @param \DateTimeImmutable $publishedAt
     *
     * @return PostDto
     */
    public function setPublishedAt(\DateTimeImmutable $publishedAt) : PostDto
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * @return \string[]
     */
    public function getTags() : array
    {
        return $this->tags;
    }

    /**
     * @param \string[] $tags
     *
     * @return PostDto
     */
    public function setTags(array $tags) : PostDto
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return int
     */
    public function getAuthorId() : int
    {
        return $this->authorId;
    }

    /**
     * @param int $authorId
     *
     * @return PostDto
     */
    public function setAuthorId(int $authorId) : PostDto
    {
        $this->authorId = $authorId;

        return $this;
    }

    public function serialize()
    {
        return serialize(
            [
                $this->getTitle(),
                $this->getSummary(),
                $this->getContent(),
                $this->getPublishedAt(),
                $this->getTags(),
                $this->getAuthorId(),
            ]
        );
    }

    public function unserialize($serialized)
    {
        list(
            $this->title,
            $this->summary,
            $this->content,
            $this->publishedAt,
            $this->tags,
            $this->authorId
            ) = unserialize($serialized);
    }
}

