<?php
declare(strict_types=1);

namespace App\Service\Creation\Post;

/**
 * Class CommentDto
 */
class CommentDto implements \Serializable
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var \DateTimeImmutable
     */
    private $publishedAt;

    /**
     * @var int
     */
    private $authorId;

    /**
     * @var int
     */
    private $postId;

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
     * @return CommentDto
     */
    public function setContent(string $content) : CommentDto
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
     * @return CommentDto
     */
    public function setPublishedAt(\DateTimeImmutable $publishedAt) : CommentDto
    {
        $this->publishedAt = $publishedAt;

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
     * @return CommentDto
     */
    public function setAuthorId(int $authorId) : CommentDto
    {
        $this->authorId = $authorId;

        return $this;
    }

    /**
     * @return int
     */
    public function getPostId() : int
    {
        return $this->postId;
    }

    /**
     * @param int $postId
     *
     * @return CommentDto
     */
    public function setPostId(int $postId) : CommentDto
    {
        $this->postId = $postId;

        return $this;
    }

    public function serialize()
    {
        return serialize(
            [
                $this->getContent(),
                $this->getPublishedAt(),
                $this->getAuthorId(),
                $this->getPostId(),
            ]
        );
    }

    public function unserialize($serialized)
    {
        list(
            $this->content,
            $this->publishedAt,
            $this->authorId,
            $this->postId
            ) = unserialize($serialized);
    }
}
