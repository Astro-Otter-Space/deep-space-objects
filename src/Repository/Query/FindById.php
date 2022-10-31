<?php

final class FindById
{

    /**
     * @param string $id
     * @return MatchQuery
     */
    public function __invoke(string $id): MatchQuery
    {
        $matchQuery = new Query\MatchQuery();
        $matchQuery->setField('id', $id);

        return $matchQuery;
    }

}
