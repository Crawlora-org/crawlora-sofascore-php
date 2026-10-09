<?php

declare(strict_types=1);

namespace Crawlora\Sofascore;

class CrawloraException extends \RuntimeException
{
    public function __construct(string $message, public readonly ?int $status = null, public readonly ?string $operationId = null, public readonly ?string $responseBody = null, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}

class ClientException extends CrawloraException {}
class ServerException extends CrawloraException {}
class NetworkException extends CrawloraException {}

final class Client
{
    private static array $operations;
    private bool $closed = false;
    private string $apiKey;
    private string $baseUrl;
    private float $timeout;
    private ?\Closure $transport;

    public const PLATFORM = 'sofascore';
    public const VERSION = '0.2.0';
    public const OPERATION_COUNT = 43;
    public const OPERATION_IDS = ["sofascore-categories", "sofascore-category-tournaments", "sofascore-event", "sofascore-event-best-players", "sofascore-event-comments", "sofascore-event-graph", "sofascore-event-h2h", "sofascore-event-incidents", "sofascore-event-lineups", "sofascore-event-odds", "sofascore-event-player-statistics", "sofascore-event-shotmap", "sofascore-event-statistics", "sofascore-live-events", "sofascore-manager", "sofascore-manager-events", "sofascore-player", "sofascore-player-season-statistics", "sofascore-player-statistics-seasons", "sofascore-player-transfers", "sofascore-ranking-types", "sofascore-rankings", "sofascore-round-events", "sofascore-scheduled-events", "sofascore-scheduled-tournaments", "sofascore-search", "sofascore-season-events", "sofascore-sports", "sofascore-standings", "sofascore-team", "sofascore-team-events", "sofascore-team-of-the-week", "sofascore-team-of-the-week-periods", "sofascore-team-players", "sofascore-team-season-statistics", "sofascore-team-statistics-seasons", "sofascore-team-transfers", "sofascore-tournament-info", "sofascore-tournament-player-statistics", "sofascore-tournament-rounds", "sofascore-tournament-seasons", "sofascore-tournament-top-players", "sofascore-tournament-top-teams"];

    public function __construct(?string $apiKey = null, string $baseUrl = 'https://api.crawlora.net/api/v1', float $timeout = 30.0, ?callable $transport = null)
    {
        $this->apiKey = $apiKey ?? (getenv('CRAWLORA_API_KEY') ?: '');
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
        $this->transport = $transport === null ? null : \Closure::fromCallable($transport);
        self::$operations ??= json_decode(<<<'JSON'
{"sofascore-categories": {"id": "sofascore-categories", "method": "GET", "params": [{"description": "Sport key", "enum": ["american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "mma", "minifootball", "padel", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "required": true, "type": "string", "x-example": "football"}], "path": "/sofascore/categories", "pathParams": [], "produces": ["application/json"], "queryParams": [{"enum": ["american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "mma", "minifootball", "padel", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-category-tournaments": {"id": "sofascore-category-tournaments", "method": "GET", "params": [{"description": "Numeric SofaScore category id from the categories endpoint", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "1"}], "path": "/sofascore/category-tournaments", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event": {"id": "sofascore-event", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "14025013"}], "path": "/sofascore/event", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-best-players": {"id": "sofascore-event-best-players", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "16363867"}], "path": "/sofascore/event-best-players", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-comments": {"id": "sofascore-event-comments", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "16363867"}], "path": "/sofascore/event-comments", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-graph": {"id": "sofascore-event-graph", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "16363867"}], "path": "/sofascore/event-graph", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-h2h": {"id": "sofascore-event-h2h", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "14025013"}], "path": "/sofascore/event-h2h", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-incidents": {"id": "sofascore-event-incidents", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "14025013"}], "path": "/sofascore/event-incidents", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-lineups": {"id": "sofascore-event-lineups", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "14025013"}], "path": "/sofascore/event-lineups", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-odds": {"id": "sofascore-event-odds", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "14025013"}], "path": "/sofascore/event-odds", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-player-statistics": {"id": "sofascore-event-player-statistics", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "16363867"}, {"description": "Numeric SofaScore player id that took part in the match", "in": "query", "name": "player_id", "required": true, "type": "string", "x-example": "839956"}], "path": "/sofascore/event-player-statistics", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "player_id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-shotmap": {"id": "sofascore-event-shotmap", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "16363867"}], "path": "/sofascore/event-shotmap", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-statistics": {"id": "sofascore-event-statistics", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "14025013"}], "path": "/sofascore/event-statistics", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-live-events": {"id": "sofascore-live-events", "method": "GET", "params": [{"description": "Sport key", "enum": ["american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "mma", "minifootball", "padel", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "required": true, "type": "string", "x-example": "football"}], "path": "/sofascore/live-events", "pathParams": [], "produces": ["application/json"], "queryParams": [{"enum": ["american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "mma", "minifootball", "padel", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-manager": {"id": "sofascore-manager", "method": "GET", "params": [{"description": "Numeric SofaScore manager id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "794873"}], "path": "/sofascore/manager", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-manager-events": {"id": "sofascore-manager-events", "method": "GET", "params": [{"description": "Numeric SofaScore manager id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "794873"}, {"description": "Zero-based page number", "in": "query", "name": "page", "type": "integer", "x-example": 0}], "path": "/sofascore/manager-events", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "page", "type": "integer"}], "security": ["ApiKeyAuth"]}, "sofascore-player": {"id": "sofascore-player", "method": "GET", "params": [{"description": "Numeric SofaScore player id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "855833"}], "path": "/sofascore/player", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-player-season-statistics": {"id": "sofascore-player-season-statistics", "method": "GET", "params": [{"description": "Numeric SofaScore player id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "839956"}, {"description": "Numeric SofaScore unique-tournament (competition) id from player-statistics-seasons", "in": "query", "name": "tournament_id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id from player-statistics-seasons", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}, {"default": "overall", "description": "Statistics view", "enum": ["overall", "home", "away", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string"}], "path": "/sofascore/player-season-statistics", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "tournament_id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"enum": ["overall", "home", "away", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-player-statistics-seasons": {"id": "sofascore-player-statistics-seasons", "method": "GET", "params": [{"description": "Numeric SofaScore player id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "839956"}], "path": "/sofascore/player-statistics-seasons", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-player-transfers": {"id": "sofascore-player-transfers", "method": "GET", "params": [{"description": "Numeric SofaScore player id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "839956"}], "path": "/sofascore/player-transfers", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-ranking-types": {"id": "sofascore-ranking-types", "method": "GET", "params": [], "path": "/sofascore/ranking-types", "pathParams": [], "produces": ["application/json"], "queryParams": [], "security": ["ApiKeyAuth"]}, "sofascore-rankings": {"id": "sofascore-rankings", "method": "GET", "params": [{"description": "Ranking id from sofascore-ranking-types", "enum": [1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 34, 35, 36, 37, 40, 41, 42, 43, 44, 45, 46], "in": "query", "name": "type", "required": true, "type": "integer", "x-example": 5}, {"description": "Return only the first N rows, 1 to 500. Omit to return every row", "in": "query", "maximum": 500, "minimum": 1, "name": "limit", "type": "integer", "x-example": 10}], "path": "/sofascore/rankings", "pathParams": [], "produces": ["application/json"], "queryParams": [{"enum": ["1", "2", "3", "4", "5", "6", "7", "8", "9", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "34", "35", "36", "37", "40", "41", "42", "43", "44", "45", "46"], "in": "query", "name": "type", "required": true, "type": "integer"}, {"in": "query", "name": "limit", "type": "integer"}], "security": ["ApiKeyAuth"]}, "sofascore-round-events": {"id": "sofascore-round-events", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "76986"}, {"description": "Round number", "in": "query", "name": "round", "required": true, "type": "integer", "x-example": 1}, {"description": "Round slug from tournament-rounds, for example round-of-16; required for knockout and named cup rounds", "in": "query", "name": "slug", "type": "string", "x-example": "round-of-16"}, {"description": "Round prefix from tournament-rounds, for example Qualification; only valid together with slug", "in": "query", "name": "prefix", "type": "string", "x-example": "Qualification"}], "path": "/sofascore/round-events", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"in": "query", "name": "round", "required": true, "type": "integer"}, {"in": "query", "name": "slug", "type": "string"}, {"in": "query", "name": "prefix", "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-scheduled-events": {"id": "sofascore-scheduled-events", "method": "GET", "params": [{"description": "Numeric SofaScore category id from the categories endpoint", "in": "query", "name": "category_id", "required": true, "type": "string", "x-example": "13"}, {"description": "UTC calendar date, YYYY-MM-DD", "in": "query", "name": "date", "required": true, "type": "string", "x-example": "2026-10-08"}], "path": "/sofascore/scheduled-events", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "category_id", "required": true, "type": "string"}, {"in": "query", "name": "date", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-scheduled-tournaments": {"id": "sofascore-scheduled-tournaments", "method": "GET", "params": [{"description": "Sport key", "enum": ["american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "mma", "minifootball", "padel", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "required": true, "type": "string", "x-example": "football"}, {"description": "UTC calendar date, YYYY-MM-DD", "in": "query", "name": "date", "required": true, "type": "string", "x-example": "2026-10-08"}, {"description": "One-based page number; defaults to 1", "in": "query", "maximum": 100, "minimum": 1, "name": "page", "type": "integer", "x-example": 1}], "path": "/sofascore/scheduled-tournaments", "pathParams": [], "produces": ["application/json"], "queryParams": [{"enum": ["american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "mma", "minifootball", "padel", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "required": true, "type": "string"}, {"in": "query", "name": "date", "required": true, "type": "string"}, {"in": "query", "name": "page", "type": "integer"}], "security": ["ApiKeyAuth"]}, "sofascore-search": {"id": "sofascore-search", "method": "GET", "params": [{"description": "Free-text search query", "in": "query", "name": "q", "required": true, "type": "string", "x-example": "barcelona"}], "path": "/sofascore/search", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "q", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-season-events": {"id": "sofascore-season-events", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}, {"description": "Upcoming fixtures or finished results", "enum": ["next", "last"], "in": "query", "name": "direction", "required": true, "type": "string", "x-example": "last"}, {"description": "Zero-based page number. Defaults to 0", "in": "query", "minimum": 0, "name": "page", "type": "integer", "x-example": 0}], "path": "/sofascore/season-events", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"enum": ["next", "last"], "in": "query", "name": "direction", "required": true, "type": "string"}, {"in": "query", "name": "page", "type": "integer"}], "security": ["ApiKeyAuth"]}, "sofascore-sports": {"id": "sofascore-sports", "method": "GET", "params": [], "path": "/sofascore/sports", "pathParams": [], "produces": ["application/json"], "queryParams": [], "security": ["ApiKeyAuth"]}, "sofascore-standings": {"id": "sofascore-standings", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "76986"}, {"description": "Standings variant", "enum": ["total", "home", "away"], "in": "query", "name": "type", "required": true, "type": "string", "x-example": "total"}], "path": "/sofascore/standings", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"enum": ["total", "home", "away"], "in": "query", "name": "type", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team": {"id": "sofascore-team", "method": "GET", "params": [{"description": "Numeric SofaScore team id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "2817"}], "path": "/sofascore/team", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team-events": {"id": "sofascore-team-events", "method": "GET", "params": [{"description": "Numeric SofaScore team id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "2817"}, {"description": "Fixture direction", "enum": ["next", "last"], "in": "query", "name": "direction", "required": true, "type": "string", "x-example": "next"}, {"description": "Zero-based page number", "in": "query", "name": "page", "type": "integer", "x-example": 0}], "path": "/sofascore/team-events", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"enum": ["next", "last"], "in": "query", "name": "direction", "required": true, "type": "string"}, {"in": "query", "name": "page", "type": "integer"}], "security": ["ApiKeyAuth"]}, "sofascore-team-of-the-week": {"id": "sofascore-team-of-the-week", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}, {"description": "Numeric period id from sofascore-team-of-the-week-periods", "in": "query", "name": "period", "required": true, "type": "string", "x-example": "29321"}], "path": "/sofascore/team-of-the-week", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"in": "query", "name": "period", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team-of-the-week-periods": {"id": "sofascore-team-of-the-week-periods", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}], "path": "/sofascore/team-of-the-week-periods", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team-players": {"id": "sofascore-team-players", "method": "GET", "params": [{"description": "Numeric SofaScore team id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "2817"}], "path": "/sofascore/team-players", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team-season-statistics": {"id": "sofascore-team-season-statistics", "method": "GET", "params": [{"description": "Numeric SofaScore team id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore unique-tournament (competition) id from team-statistics-seasons", "in": "query", "name": "tournament_id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id from team-statistics-seasons", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}, {"default": "overall", "description": "Statistics view", "enum": ["overall", "home", "away", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string"}], "path": "/sofascore/team-season-statistics", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "tournament_id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"enum": ["overall", "home", "away", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team-statistics-seasons": {"id": "sofascore-team-statistics-seasons", "method": "GET", "params": [{"description": "Numeric SofaScore team id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}], "path": "/sofascore/team-statistics-seasons", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team-transfers": {"id": "sofascore-team-transfers", "method": "GET", "params": [{"description": "Numeric SofaScore team id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}], "path": "/sofascore/team-transfers", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-tournament-info": {"id": "sofascore-tournament-info", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id. Omit for competition metadata only", "in": "query", "name": "season", "type": "string", "x-example": "96668"}], "path": "/sofascore/tournament-info", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-tournament-player-statistics": {"id": "sofascore-tournament-player-statistics", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id of a football competition", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}, {"description": "Statistic to sort by. Defaults to rating", "enum": ["rating", "goals", "expectedGoals", "assists", "successfulDribbles", "tackles", "accuratePassesPercentage", "bigChancesMissed", "totalShots", "goalConversionPercentage", "interceptions", "clearances", "errorLeadToGoal", "outfielderBlocks", "bigChancesCreated", "accuratePasses", "keyPasses", "saves", "cleanSheet", "penaltySave", "savedShotsFromInsideTheBox", "runsOut"], "in": "query", "name": "order", "type": "string", "x-example": "goals"}, {"description": "Sort direction. Defaults to desc", "enum": ["desc", "asc"], "in": "query", "name": "direction", "type": "string", "x-example": "desc"}, {"description": "How statistics are accumulated. Defaults to total", "enum": ["total", "perGame", "per90"], "in": "query", "name": "accumulation", "type": "string", "x-example": "total"}, {"description": "Statistic columns returned. Defaults to summary", "enum": ["summary", "attack", "defence", "passing", "goalkeeper"], "in": "query", "name": "group", "type": "string", "x-example": "summary"}, {"description": "Rows per page, 1 to 100. Defaults to 20", "in": "query", "maximum": 100, "minimum": 1, "name": "limit", "type": "integer", "x-example": 20}, {"description": "Rows to skip. Defaults to 0", "in": "query", "minimum": 0, "name": "offset", "type": "integer", "x-example": 0}], "path": "/sofascore/tournament-player-statistics", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"enum": ["rating", "goals", "expectedGoals", "assists", "successfulDribbles", "tackles", "accuratePassesPercentage", "bigChancesMissed", "totalShots", "goalConversionPercentage", "interceptions", "clearances", "errorLeadToGoal", "outfielderBlocks", "bigChancesCreated", "accuratePasses", "keyPasses", "saves", "cleanSheet", "penaltySave", "savedShotsFromInsideTheBox", "runsOut"], "in": "query", "name": "order", "type": "string"}, {"enum": ["desc", "asc"], "in": "query", "name": "direction", "type": "string"}, {"enum": ["total", "perGame", "per90"], "in": "query", "name": "accumulation", "type": "string"}, {"enum": ["summary", "attack", "defence", "passing", "goalkeeper"], "in": "query", "name": "group", "type": "string"}, {"in": "query", "name": "limit", "type": "integer"}, {"in": "query", "name": "offset", "type": "integer"}], "security": ["ApiKeyAuth"]}, "sofascore-tournament-rounds": {"id": "sofascore-tournament-rounds", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "7"}, {"description": "Numeric SofaScore season id from tournament-seasons", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "76953"}], "path": "/sofascore/tournament-rounds", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-tournament-seasons": {"id": "sofascore-tournament-seasons", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}], "path": "/sofascore/tournament-seasons", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-tournament-top-players": {"id": "sofascore-tournament-top-players", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}, {"description": "Statistics scope. Defaults to overall", "enum": ["overall", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string", "x-example": "overall"}, {"description": "Rows per category, 1 to 50. Omit to return every row", "in": "query", "maximum": 50, "minimum": 1, "name": "limit", "type": "integer", "x-example": 5}], "path": "/sofascore/tournament-top-players", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"enum": ["overall", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string"}, {"in": "query", "name": "limit", "type": "integer"}], "security": ["ApiKeyAuth"]}, "sofascore-tournament-top-teams": {"id": "sofascore-tournament-top-teams", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}, {"description": "Statistics scope. Defaults to overall", "enum": ["overall", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string", "x-example": "overall"}, {"description": "Rows per category, 1 to 50. Omit to return every row", "in": "query", "maximum": 50, "minimum": 1, "name": "limit", "type": "integer", "x-example": 5}], "path": "/sofascore/tournament-top-teams", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"enum": ["overall", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string"}, {"in": "query", "name": "limit", "type": "integer"}], "security": ["ApiKeyAuth"]}}
JSON, true, 512, JSON_THROW_ON_ERROR);
    }

    public function request(string $operationId, array $params = [], string $responseType = 'auto'): mixed
    {
        if ($this->closed) {
            throw new ClientException('Client is closed', null, $operationId);
        }
        $operation = self::$operations[$operationId] ?? null;
        if ($operation === null) {
            throw new ClientException('Unknown operation: ' . $operationId, null, $operationId);
        }
        if ($this->apiKey === '') {
            throw new ClientException('Crawlora API key is required', null, $operationId);
        }
        $url = $this->buildUrl($operation, $params);
        $headers = [
            'x-api-key: ' . $this->apiKey,
            'User-Agent: crawlora-sofascore-php/0.2.0',
            'Accept: ' . (in_array('text/plain', $operation['produces'], true) ? 'application/json, text/plain' : 'application/json'),
        ];
        try {
            [$status, $contentType, $body] = $this->send($url, $headers, $operationId);
        } catch (CrawloraException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw new NetworkException('Crawlora request failed: ' . $exception->getMessage(), null, $operationId, null, $exception);
        }
        if ($status < 200 || $status >= 300) {
            $class = $status >= 500 ? ServerException::class : ClientException::class;
            throw new $class('Crawlora returned HTTP ' . $status, $status, $operationId, $body);
        }
        return $this->parseResponse($body, $contentType, $operation, $params, $responseType);
    }

    public function close(): void
    {
        $this->closed = true;
    }

    public function isClosed(): bool
    {
        return $this->closed;
    }

    public function operationCount(): int
    {
        return self::OPERATION_COUNT;
    }

    public function operationIds(): array
    {
        return self::OPERATION_IDS;
    }

    public function operations(): array
    {
        return self::$operations;
    }

    public function categories(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-categories", $params, $responseType);
    }
    public function category_tournaments(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-category-tournaments", $params, $responseType);
    }
    public function event(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event", $params, $responseType);
    }
    public function event_best_players(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-best-players", $params, $responseType);
    }
    public function event_comments(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-comments", $params, $responseType);
    }
    public function event_graph(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-graph", $params, $responseType);
    }
    public function event_h2h(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-h2h", $params, $responseType);
    }
    public function event_incidents(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-incidents", $params, $responseType);
    }
    public function event_lineups(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-lineups", $params, $responseType);
    }
    public function event_odds(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-odds", $params, $responseType);
    }
    public function event_player_statistics(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-player-statistics", $params, $responseType);
    }
    public function event_shotmap(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-shotmap", $params, $responseType);
    }
    public function event_statistics(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-statistics", $params, $responseType);
    }
    public function live_events(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-live-events", $params, $responseType);
    }
    public function manager(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-manager", $params, $responseType);
    }
    public function manager_events(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-manager-events", $params, $responseType);
    }
    public function player(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-player", $params, $responseType);
    }
    public function player_season_statistics(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-player-season-statistics", $params, $responseType);
    }
    public function player_statistics_seasons(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-player-statistics-seasons", $params, $responseType);
    }
    public function player_transfers(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-player-transfers", $params, $responseType);
    }
    public function ranking_types(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-ranking-types", $params, $responseType);
    }
    public function rankings(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-rankings", $params, $responseType);
    }
    public function round_events(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-round-events", $params, $responseType);
    }
    public function scheduled_events(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-scheduled-events", $params, $responseType);
    }
    public function scheduled_tournaments(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-scheduled-tournaments", $params, $responseType);
    }
    public function search(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-search", $params, $responseType);
    }
    public function season_events(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-season-events", $params, $responseType);
    }
    public function sports(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-sports", $params, $responseType);
    }
    public function standings(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-standings", $params, $responseType);
    }
    public function team(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-team", $params, $responseType);
    }
    public function team_events(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-team-events", $params, $responseType);
    }
    public function team_of_the_week(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-team-of-the-week", $params, $responseType);
    }
    public function team_of_the_week_periods(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-team-of-the-week-periods", $params, $responseType);
    }
    public function team_players(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-team-players", $params, $responseType);
    }
    public function team_season_statistics(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-team-season-statistics", $params, $responseType);
    }
    public function team_statistics_seasons(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-team-statistics-seasons", $params, $responseType);
    }
    public function team_transfers(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-team-transfers", $params, $responseType);
    }
    public function tournament_info(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-tournament-info", $params, $responseType);
    }
    public function tournament_player_statistics(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-tournament-player-statistics", $params, $responseType);
    }
    public function tournament_rounds(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-tournament-rounds", $params, $responseType);
    }
    public function tournament_seasons(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-tournament-seasons", $params, $responseType);
    }
    public function tournament_top_players(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-tournament-top-players", $params, $responseType);
    }
    public function tournament_top_teams(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-tournament-top-teams", $params, $responseType);
    }

    private function buildUrl(array $operation, array $params): string
    {
        $known = array_column($operation['params'], 'name');
        $unknown = array_diff(array_keys($params), $known, ['response_type', '_response_type']);
        if ($unknown !== []) {
            throw new ClientException('Unknown parameters: ' . implode(', ', $unknown), null, $operation['id']);
        }
        $path = $operation['path'];
        foreach ($operation['params'] as $param) {
            if ($param['in'] !== 'path') {
                continue;
            }
            $name = $param['name'];
            if (!array_key_exists($name, $params) || $params[$name] === null) {
                throw new ClientException('Missing path parameter: ' . $name, null, $operation['id']);
            }
            $path = str_replace('{' . $name . '}', rawurlencode((string) $params[$name]), $path);
        }
        $pairs = [];
        foreach ($operation['queryParams'] as $param) {
            $name = $param['name'];
            $value = $params[$name] ?? ($param['default'] ?? null);
            if ($value === null) {
                if ($param['required'] ?? false) {
                    throw new ClientException('Missing query parameter: ' . $name, null, $operation['id']);
                }
                continue;
            }
            $enumValues = $param['enum'] ?? ($param['items']['enum'] ?? null);
            $values = is_array($value) ? $value : [$value];
            $invalidEnum = false;
            foreach ($values as $item) {
                if ($enumValues !== null && !in_array((string) $item, array_map('strval', $enumValues), true)) {
                    $invalidEnum = true;
                    break;
                }
            }
            if ($invalidEnum) {
                throw new ClientException('Invalid value for ' . $name, null, $operation['id']);
            }
            if (is_array($value)) {
                $format = $param['collectionFormat'] ?? 'csv';
                if ($format === 'multi') {
                    foreach ($value as $item) {
                        $pairs[] = [rawurlencode($name), rawurlencode($this->stringify($item))];
                    }
                } else {
                    $separator = ['csv' => ',', 'ssv' => ' ', 'tsv' => "\t", 'pipes' => '|'][$format] ?? ',';
                    $pairs[] = [rawurlencode($name), rawurlencode(implode($separator, array_map([$this, 'stringify'], $value)))];
                }
            } else {
                $pairs[] = [rawurlencode($name), rawurlencode($this->stringify($value))];
            }
        }
        $query = implode('&', array_map(static fn(array $pair): string => $pair[0] . '=' . $pair[1], $pairs));
        return $this->baseUrl . $path . ($query === '' ? '' : '?' . $query);
    }

    private function stringify(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (is_array($value)) {
            return json_encode($value, JSON_THROW_ON_ERROR);
        }
        return (string) $value;
    }

    private function send(string $url, array $headers, string $operationId): array
    {
        if ($this->transport !== null) {
            $result = ($this->transport)($url, $headers, $this->timeout);
            return [(int) $result['status'], (string) ($result['content_type'] ?? ''), (string) ($result['body'] ?? '')];
        }
        $handle = curl_init($url);
        if ($handle === false) {
            throw new NetworkException('Could not initialize cURL', null, $operationId);
        }
        curl_setopt_array($handle, [
            CURLOPT_HTTPGET => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT_MS => (int) ($this->timeout * 1000),
            CURLOPT_CONNECTTIMEOUT_MS => (int) ($this->timeout * 1000),
        ]);
        $body = curl_exec($handle);
        if ($body === false) {
            $message = curl_error($handle);
            curl_close($handle);
            throw new NetworkException('Crawlora request failed: ' . $message, null, $operationId);
        }
        $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        $contentType = (string) curl_getinfo($handle, CURLINFO_CONTENT_TYPE);
        curl_close($handle);
        return [$status, $contentType, (string) $body];
    }

    private function parseResponse(string $body, string $contentType, array $operation, array $params, string $responseType): mixed
    {
        if (!in_array($responseType, ['auto', 'json', 'text'], true)) {
            throw new ClientException('responseType must be auto, json, or text', null, $operation['id']);
        }
        $format = null;
        foreach ($operation['params'] as $param) {
            if ($param['name'] === 'format') {
                $format = $param;
                break;
            }
        }
        $textFormats = array_values(array_filter($format['enum'] ?? [], static fn($value): bool => !in_array(strtolower((string) $value), ['json', 'application/json'], true)));
        $rawFormat = isset($params['format']) && in_array((string) $params['format'], array_map('strval', $textFormats), true);
        $jsonFormat = isset($params['format']) && in_array(strtolower((string) $params['format']), ['json', 'application/json'], true);
        $isJson = $jsonFormat || stripos($contentType, 'json') !== false || $operation['produces'] === ['application/json'];
        if ($responseType === 'text' || $rawFormat || ($responseType === 'auto' && !$isJson)) {
            return $body;
        }
        try {
            return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new CrawloraException('Invalid JSON response from Crawlora: ' . $exception->getMessage(), null, $operation['id'], $body, $exception);
        }
    }
}
