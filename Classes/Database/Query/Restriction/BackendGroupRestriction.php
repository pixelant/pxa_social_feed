<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Database\Query\Restriction;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\QueryRestrictionInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Restrict access by BE user group
 */
class BackendGroupRestriction implements QueryRestrictionInterface
{
    /**
     * @var string
     */
    protected $groupFieldName = 'be_group';

    /**
     * @var BackendUserAuthentication|null
     */
    protected $backendUserAuth = null;

    /**
     * Initialize
     */
    public function __construct()
    {
        if (isset($GLOBALS['BE_USER'])) {
            $this->backendUserAuth = $GLOBALS['BE_USER'];
        }
    }

    /**
     * @inheritDoc
     */
    public function buildExpression(array $queriedTables, ExpressionBuilder $expressionBuilder): CompositeExpression
    {
        $constraints = [];
        if ($this->backendUserAuth !== null && !$this->backendUserAuth->isAdmin()) {
            foreach ($queriedTables as $tableAlias => $tableName) {
                $fieldName = $tableAlias . '.' . $this->groupFieldName;
                // Allow records where no group access has been configured (field values NULL, 0 or empty string)
                $constraints = [
                    $expressionBuilder->isNull($fieldName),
                    $expressionBuilder->eq($fieldName, $expressionBuilder->literal('')),
                    $expressionBuilder->eq($fieldName, $expressionBuilder->literal('0')),
                ];

                $backendGroupIds = GeneralUtility::intExplode(',', $this->backendUserAuth->groupList);
                foreach ($backendGroupIds as $backendGroupId) {
                    $constraints[] = $expressionBuilder->inSet(
                        $fieldName,
                        $expressionBuilder->literal((string)$backendGroupId)
                    );
                }
            }
        }

        return $expressionBuilder->orX(...$constraints);
    }
}
