<?php

namespace App\Twig\Components;

use App\Entity\Conference;
use App\Repository\CommentRepository;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\Pagination\LiveComponent\ComponentWithPaginationTrait;
use Symfony\UX\Pagination\PaginationBuilder;
use Symfony\UX\Pagination\PaginatorInterface;

/**
 * The published comments of a conference, paginated without full page reloads.
 */
#[AsLiveComponent]
final class CommentList
{
    use DefaultActionTrait;
    use ComponentWithPaginationTrait;

    // Not writable: the browser sends it back signed, so it cannot be swapped for another conference
    #[LiveProp]
    public Conference $conference;

    public function __construct(
        private readonly PaginatorInterface $paginator,
        private readonly CommentRepository $commentRepository,
    ) {
    }

    protected function createPagination(): PaginationBuilder
    {
        return $this->paginator
            ->query($this->commentRepository->
            createPublishedByConferenceQueryBuilder($this->conference))
            ->perPage(CommentRepository::COMMENTS_PER_PAGE);
    }
}
