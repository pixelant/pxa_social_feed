<?php

// Copyright JAKOTA Design Group GmbH. All rights reserved.
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Domain\Model;

class FacebookPage
{
    /**
     * @var string
     */
    protected string $accessToken;

    /**
     * @var string
     */
    protected string $category;

    /**
     * @var array<int, array<string, string>>
     */
    protected array $categoryList;

    /**
     * @var string
     */
    protected string $id;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string[]
     */
    protected array $tasks;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->accessToken = strval($data['access_token'] ?? '');
        $this->category = strval($data['category'] ?? '');
        $this->categoryList = is_array($data['category_list']) ? $data['category_list'] : [];
        $this->name = strval($data['name'] ?? '');
        $this->id = strval($data['id'] ?? '');
        $this->tasks = is_array($data['tasks']) ? $data['tasks'] : [];
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getCategoryList(): array
    {
        return $this->categoryList;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }
}
