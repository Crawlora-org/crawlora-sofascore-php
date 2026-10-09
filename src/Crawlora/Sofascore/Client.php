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
    public const VERSION = '0.3.3';
    public const OPERATION_COUNT = 109;
    public const OPERATION_IDS = ["sofascore-categories", "sofascore-category-tournaments", "sofascore-draft", "sofascore-draft-picks", "sofascore-esports-game", "sofascore-event", "sofascore-event-at-bat-pitches", "sofascore-event-at-bats", "sofascore-event-average-positions", "sofascore-event-baseball-top-performers", "sofascore-event-best-players", "sofascore-event-comments", "sofascore-event-esports-games", "sofascore-event-graph", "sofascore-event-h2h", "sofascore-event-highlights", "sofascore-event-incidents", "sofascore-event-innings", "sofascore-event-lineups", "sofascore-event-managers", "sofascore-event-odds", "sofascore-event-player-heatmap", "sofascore-event-player-statistics", "sofascore-event-point-by-point", "sofascore-event-pregame-form", "sofascore-event-shotmap", "sofascore-event-statistics", "sofascore-event-team-heatmap", "sofascore-event-team-streaks", "sofascore-event-tennis-power", "sofascore-event-tv-channels", "sofascore-event-votes", "sofascore-live-events", "sofascore-manager", "sofascore-manager-events", "sofascore-mma-card", "sofascore-mma-schedule", "sofascore-odds-dropping", "sofascore-odds-winning", "sofascore-player", "sofascore-player-attributes", "sofascore-player-events", "sofascore-player-last-year-summary", "sofascore-player-national-team-statistics", "sofascore-player-penalty-history", "sofascore-player-ratings", "sofascore-player-season-heatmap", "sofascore-player-season-statistics", "sofascore-player-statistical-rankings", "sofascore-player-statistics-seasons", "sofascore-player-tournaments", "sofascore-player-transfers", "sofascore-ranking-types", "sofascore-rankings", "sofascore-referee", "sofascore-referee-events", "sofascore-referee-statistics", "sofascore-round-events", "sofascore-scheduled-events", "sofascore-scheduled-tournaments", "sofascore-search", "sofascore-search-typed", "sofascore-season-events", "sofascore-sports", "sofascore-stage", "sofascore-stage-categories", "sofascore-stage-driver-performance", "sofascore-stage-featured", "sofascore-stage-schedule", "sofascore-stage-seasons", "sofascore-stage-standings", "sofascore-stage-substages", "sofascore-standings", "sofascore-team", "sofascore-team-achievements", "sofascore-team-events", "sofascore-team-goal-distributions", "sofascore-team-near-events", "sofascore-team-of-the-week", "sofascore-team-of-the-week-periods", "sofascore-team-performance", "sofascore-team-player-statistics", "sofascore-team-player-statistics-seasons", "sofascore-team-players", "sofascore-team-rankings", "sofascore-team-season-statistics", "sofascore-team-statistics-seasons", "sofascore-team-top-players", "sofascore-team-tournaments", "sofascore-team-transfers", "sofascore-tennis-player-grand-slam-results", "sofascore-tournament-cuptree", "sofascore-tournament-info", "sofascore-tournament-player-of-the-season", "sofascore-tournament-player-statistics", "sofascore-tournament-rounds", "sofascore-tournament-seasons", "sofascore-tournament-statistics-info", "sofascore-tournament-team-of-the-season", "sofascore-tournament-teams", "sofascore-tournament-top-players", "sofascore-tournament-top-teams", "sofascore-tournament-venues", "sofascore-tournament-winners", "sofascore-tournaments-with-feature", "sofascore-trending-events", "sofascore-trending-players", "sofascore-venue", "sofascore-venue-events"];

    public function __construct(?string $apiKey = null, string $baseUrl = 'https://api.crawlora.net/api/v1', float $timeout = 30.0, ?callable $transport = null)
    {
        $this->apiKey = $apiKey ?? (getenv('CRAWLORA_API_KEY') ?: '');
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
        $this->transport = $transport === null ? null : \Closure::fromCallable($transport);
        self::$operations ??= json_decode(<<<'JSON'
{"sofascore-categories": {"id": "sofascore-categories", "method": "GET", "params": [{"description": "Sport key", "enum": ["american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "mma", "minifootball", "padel", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "required": true, "type": "string", "x-example": "football"}], "path": "/sofascore/categories", "pathParams": [], "produces": ["application/json"], "queryParams": [{"enum": ["american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "mma", "minifootball", "padel", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-category-tournaments": {"id": "sofascore-category-tournaments", "method": "GET", "params": [{"description": "Numeric SofaScore category id from the categories endpoint", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "1"}], "path": "/sofascore/category-tournaments", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-draft": {"id": "sofascore-draft", "method": "GET", "params": [{"description": "League whose draft to return", "enum": ["nba", "nfl"], "in": "query", "name": "league", "required": true, "type": "string", "x-example": "nba"}, {"description": "Numeric SofaScore season id of the league", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "80229"}], "path": "/sofascore/draft", "pathParams": [], "produces": ["application/json"], "queryParams": [{"enum": ["nba", "nfl"], "in": "query", "name": "league", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-draft-picks": {"id": "sofascore-draft-picks", "method": "GET", "params": [{"description": "League whose draft to return", "enum": ["nba", "nfl"], "in": "query", "name": "league", "required": true, "type": "string", "x-example": "nba"}, {"description": "Four-digit draft year", "in": "query", "name": "year", "required": true, "type": "string", "x-example": "2025"}, {"description": "Draft round: 1 to 2 for nba, 1 to 7 for nfl", "in": "query", "maximum": 7, "minimum": 1, "name": "round", "required": true, "type": "integer", "x-example": 1}], "path": "/sofascore/draft-picks", "pathParams": [], "produces": ["application/json"], "queryParams": [{"enum": ["nba", "nfl"], "in": "query", "name": "league", "required": true, "type": "string"}, {"in": "query", "name": "year", "required": true, "type": "string"}, {"in": "query", "name": "round", "required": true, "type": "integer"}], "security": ["ApiKeyAuth"]}, "sofascore-esports-game": {"id": "sofascore-esports-game", "method": "GET", "params": [{"description": "Numeric SofaScore esports game id from sofascore-event-esports-games", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "588883"}, {"description": "Which resource of the game to return", "enum": ["statistics", "lineups", "bans", "rounds"], "in": "query", "name": "part", "required": true, "type": "string", "x-example": "lineups"}], "path": "/sofascore/esports-game", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"enum": ["statistics", "lineups", "bans", "rounds"], "in": "query", "name": "part", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event": {"id": "sofascore-event", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "14025013"}], "path": "/sofascore/event", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-at-bat-pitches": {"id": "sofascore-event-at-bat-pitches", "method": "GET", "params": [{"description": "Numeric SofaScore baseball event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17199139"}, {"description": "Numeric at-bat id from sofascore-event-at-bats for the same event", "in": "query", "name": "at_bat_id", "required": true, "type": "string", "x-example": "2595879"}], "path": "/sofascore/event-at-bat-pitches", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "at_bat_id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-at-bats": {"id": "sofascore-event-at-bats", "method": "GET", "params": [{"description": "Numeric SofaScore baseball event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17199139"}], "path": "/sofascore/event-at-bats", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-average-positions": {"id": "sofascore-event-average-positions", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "16363867"}], "path": "/sofascore/event-average-positions", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-baseball-top-performers": {"id": "sofascore-event-baseball-top-performers", "method": "GET", "params": [{"description": "Numeric SofaScore baseball event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17199139"}], "path": "/sofascore/event-baseball-top-performers", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-best-players": {"id": "sofascore-event-best-players", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "16363867"}], "path": "/sofascore/event-best-players", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-comments": {"id": "sofascore-event-comments", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "16363867"}], "path": "/sofascore/event-comments", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-esports-games": {"id": "sofascore-event-esports-games", "method": "GET", "params": [{"description": "Numeric SofaScore esports event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17260338"}], "path": "/sofascore/event-esports-games", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-graph": {"id": "sofascore-event-graph", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "16363867"}], "path": "/sofascore/event-graph", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-h2h": {"id": "sofascore-event-h2h", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "14025013"}], "path": "/sofascore/event-h2h", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-highlights": {"id": "sofascore-event-highlights", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "16363867"}], "path": "/sofascore/event-highlights", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-incidents": {"id": "sofascore-event-incidents", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "14025013"}], "path": "/sofascore/event-incidents", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-innings": {"id": "sofascore-event-innings", "method": "GET", "params": [{"description": "Numeric SofaScore cricket event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "16253632"}], "path": "/sofascore/event-innings", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-lineups": {"id": "sofascore-event-lineups", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "14025013"}], "path": "/sofascore/event-lineups", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-managers": {"id": "sofascore-event-managers", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "16363867"}], "path": "/sofascore/event-managers", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-odds": {"id": "sofascore-event-odds", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "14025013"}], "path": "/sofascore/event-odds", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-player-heatmap": {"id": "sofascore-event-player-heatmap", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "16363867"}, {"description": "Numeric SofaScore player id that played in the match", "in": "query", "name": "player_id", "required": true, "type": "string", "x-example": "839956"}], "path": "/sofascore/event-player-heatmap", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "player_id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-player-statistics": {"id": "sofascore-event-player-statistics", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "16363867"}, {"description": "Numeric SofaScore player id that took part in the match", "in": "query", "name": "player_id", "required": true, "type": "string", "x-example": "839956"}], "path": "/sofascore/event-player-statistics", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "player_id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-point-by-point": {"id": "sofascore-event-point-by-point", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17257382"}], "path": "/sofascore/event-point-by-point", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-pregame-form": {"id": "sofascore-event-pregame-form", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "16363867"}], "path": "/sofascore/event-pregame-form", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-shotmap": {"id": "sofascore-event-shotmap", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "16363867"}], "path": "/sofascore/event-shotmap", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-statistics": {"id": "sofascore-event-statistics", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "14025013"}], "path": "/sofascore/event-statistics", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-team-heatmap": {"id": "sofascore-event-team-heatmap", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "16363867"}, {"description": "Numeric SofaScore team id of the home or away side", "in": "query", "name": "team_id", "required": true, "type": "string", "x-example": "17"}], "path": "/sofascore/event-team-heatmap", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "team_id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-team-streaks": {"id": "sofascore-event-team-streaks", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "16363867"}], "path": "/sofascore/event-team-streaks", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-tennis-power": {"id": "sofascore-event-tennis-power", "method": "GET", "params": [{"description": "Numeric SofaScore tennis event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17257382"}], "path": "/sofascore/event-tennis-power", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-tv-channels": {"id": "sofascore-event-tv-channels", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "16363867"}, {"description": "Two-letter ISO 3166-1 alpha-2 country code. Omit to list the broadcasting countries only", "in": "query", "name": "country", "type": "string", "x-example": "GB"}], "path": "/sofascore/event-tv-channels", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "country", "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-event-votes": {"id": "sofascore-event-votes", "method": "GET", "params": [{"description": "Numeric SofaScore event (match) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "16363867"}], "path": "/sofascore/event-votes", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-live-events": {"id": "sofascore-live-events", "method": "GET", "params": [{"description": "Sport key", "enum": ["american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "mma", "minifootball", "padel", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "required": true, "type": "string", "x-example": "football"}], "path": "/sofascore/live-events", "pathParams": [], "produces": ["application/json"], "queryParams": [{"enum": ["american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "mma", "minifootball", "padel", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-manager": {"id": "sofascore-manager", "method": "GET", "params": [{"description": "Numeric SofaScore manager id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "794873"}], "path": "/sofascore/manager", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-manager-events": {"id": "sofascore-manager-events", "method": "GET", "params": [{"description": "Numeric SofaScore manager id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "794873"}, {"description": "Zero-based page number", "in": "query", "name": "page", "type": "integer", "x-example": 0}], "path": "/sofascore/manager-events", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "page", "type": "integer"}], "security": ["ApiKeyAuth"]}, "sofascore-mma-card": {"id": "sofascore-mma-card", "method": "GET", "params": [{"description": "Numeric SofaScore MMA organisation id", "in": "query", "name": "org_id", "required": true, "type": "string", "x-example": "19906"}, {"description": "Numeric card id (card_id) from sofascore-mma-schedule", "in": "query", "name": "card_id", "required": true, "type": "string", "x-example": "197311"}, {"description": "Which segment of the card to return", "enum": ["all", "maincard", "prelims", "earlyprelims"], "in": "query", "name": "part", "required": true, "type": "string", "x-example": "all"}], "path": "/sofascore/mma-card", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "org_id", "required": true, "type": "string"}, {"in": "query", "name": "card_id", "required": true, "type": "string"}, {"enum": ["all", "maincard", "prelims", "earlyprelims"], "in": "query", "name": "part", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-mma-schedule": {"id": "sofascore-mma-schedule", "method": "GET", "params": [{"description": "Numeric SofaScore MMA organisation id", "in": "query", "name": "org_id", "required": true, "type": "string", "x-example": "19906"}, {"description": "Calendar month as YYYY-MM", "in": "query", "name": "month", "required": true, "type": "string", "x-example": "2026-10"}], "path": "/sofascore/mma-schedule", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "org_id", "required": true, "type": "string"}, {"in": "query", "name": "month", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-odds-dropping": {"id": "sofascore-odds-dropping", "method": "GET", "params": [{"description": "Sport key, or all for every sport. Defaults to all", "enum": ["all", "american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "mma", "minifootball", "padel", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "type": "string", "x-example": "football"}], "path": "/sofascore/odds-dropping", "pathParams": [], "produces": ["application/json"], "queryParams": [{"enum": ["all", "american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "mma", "minifootball", "padel", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-odds-winning": {"id": "sofascore-odds-winning", "method": "GET", "params": [{"description": "Sport key, or all for every sport. Defaults to all", "enum": ["all", "american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "mma", "minifootball", "padel", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "type": "string", "x-example": "football"}], "path": "/sofascore/odds-winning", "pathParams": [], "produces": ["application/json"], "queryParams": [{"enum": ["all", "american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "mma", "minifootball", "padel", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-player": {"id": "sofascore-player", "method": "GET", "params": [{"description": "Numeric SofaScore player id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "855833"}], "path": "/sofascore/player", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-player-attributes": {"id": "sofascore-player-attributes", "method": "GET", "params": [{"description": "Numeric SofaScore player id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "839956"}], "path": "/sofascore/player-attributes", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-player-events": {"id": "sofascore-player-events", "method": "GET", "params": [{"description": "Numeric SofaScore player id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "839956"}, {"description": "Zero-based page number. Defaults to 0", "in": "query", "minimum": 0, "name": "page", "type": "integer", "x-example": 0}], "path": "/sofascore/player-events", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "page", "type": "integer"}], "security": ["ApiKeyAuth"]}, "sofascore-player-last-year-summary": {"id": "sofascore-player-last-year-summary", "method": "GET", "params": [{"description": "Numeric SofaScore player id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "839956"}], "path": "/sofascore/player-last-year-summary", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-player-national-team-statistics": {"id": "sofascore-player-national-team-statistics", "method": "GET", "params": [{"description": "Numeric SofaScore player id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "839956"}], "path": "/sofascore/player-national-team-statistics", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-player-penalty-history": {"id": "sofascore-player-penalty-history", "method": "GET", "params": [{"description": "Numeric SofaScore player id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "839956"}], "path": "/sofascore/player-penalty-history", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-player-ratings": {"id": "sofascore-player-ratings", "method": "GET", "params": [{"description": "Numeric SofaScore player id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "839956"}, {"description": "Numeric SofaScore unique-tournament (competition) id from player-statistics-seasons", "in": "query", "name": "tournament_id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id from player-statistics-seasons", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "76986"}, {"default": "overall", "description": "Statistics view", "enum": ["overall", "home", "away", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string"}], "path": "/sofascore/player-ratings", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "tournament_id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"enum": ["overall", "home", "away", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-player-season-heatmap": {"id": "sofascore-player-season-heatmap", "method": "GET", "params": [{"description": "Numeric SofaScore player id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "839956"}, {"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "tournament_id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "76986"}], "path": "/sofascore/player-season-heatmap", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "tournament_id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-player-season-statistics": {"id": "sofascore-player-season-statistics", "method": "GET", "params": [{"description": "Numeric SofaScore player id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "839956"}, {"description": "Numeric SofaScore unique-tournament (competition) id from player-statistics-seasons", "in": "query", "name": "tournament_id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id from player-statistics-seasons", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}, {"default": "overall", "description": "Statistics view", "enum": ["overall", "home", "away", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string"}], "path": "/sofascore/player-season-statistics", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "tournament_id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"enum": ["overall", "home", "away", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-player-statistical-rankings": {"id": "sofascore-player-statistical-rankings", "method": "GET", "params": [{"description": "Numeric SofaScore player id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "839956"}, {"description": "Numeric SofaScore season id from player-statistics-seasons", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}, {"default": "overall", "description": "Statistics view", "enum": ["overall"], "in": "query", "name": "type", "type": "string"}], "path": "/sofascore/player-statistical-rankings", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"enum": ["overall"], "in": "query", "name": "type", "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-player-statistics-seasons": {"id": "sofascore-player-statistics-seasons", "method": "GET", "params": [{"description": "Numeric SofaScore player id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "839956"}], "path": "/sofascore/player-statistics-seasons", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-player-tournaments": {"id": "sofascore-player-tournaments", "method": "GET", "params": [{"description": "Numeric SofaScore player id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "839956"}], "path": "/sofascore/player-tournaments", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-player-transfers": {"id": "sofascore-player-transfers", "method": "GET", "params": [{"description": "Numeric SofaScore player id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "839956"}], "path": "/sofascore/player-transfers", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-ranking-types": {"id": "sofascore-ranking-types", "method": "GET", "params": [], "path": "/sofascore/ranking-types", "pathParams": [], "produces": ["application/json"], "queryParams": [], "security": ["ApiKeyAuth"]}, "sofascore-rankings": {"id": "sofascore-rankings", "method": "GET", "params": [{"description": "Ranking id from sofascore-ranking-types", "enum": [1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 34, 35, 36, 37, 40, 41, 42, 43, 44, 45, 46], "in": "query", "name": "type", "required": true, "type": "integer", "x-example": 5}, {"description": "Return only the first N rows, 1 to 500. Omit to return every row", "in": "query", "maximum": 500, "minimum": 1, "name": "limit", "type": "integer", "x-example": 10}], "path": "/sofascore/rankings", "pathParams": [], "produces": ["application/json"], "queryParams": [{"enum": ["1", "2", "3", "4", "5", "6", "7", "8", "9", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "34", "35", "36", "37", "40", "41", "42", "43", "44", "45", "46"], "in": "query", "name": "type", "required": true, "type": "integer"}, {"in": "query", "name": "limit", "type": "integer"}], "security": ["ApiKeyAuth"]}, "sofascore-referee": {"id": "sofascore-referee", "method": "GET", "params": [{"description": "Numeric SofaScore referee id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "74264"}], "path": "/sofascore/referee", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-referee-events": {"id": "sofascore-referee-events", "method": "GET", "params": [{"description": "Numeric SofaScore referee id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "74264"}, {"description": "Zero-based page number. Defaults to 0", "in": "query", "minimum": 0, "name": "page", "type": "integer", "x-example": 0}], "path": "/sofascore/referee-events", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "page", "type": "integer"}], "security": ["ApiKeyAuth"]}, "sofascore-referee-statistics": {"id": "sofascore-referee-statistics", "method": "GET", "params": [{"description": "Numeric SofaScore referee id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "74264"}], "path": "/sofascore/referee-statistics", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-round-events": {"id": "sofascore-round-events", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "76986"}, {"description": "Round number", "in": "query", "name": "round", "required": true, "type": "integer", "x-example": 1}, {"description": "Round slug from tournament-rounds, for example round-of-16; required for knockout and named cup rounds", "in": "query", "name": "slug", "type": "string", "x-example": "round-of-16"}, {"description": "Round prefix from tournament-rounds, for example Qualification; only valid together with slug", "in": "query", "name": "prefix", "type": "string", "x-example": "Qualification"}], "path": "/sofascore/round-events", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"in": "query", "name": "round", "required": true, "type": "integer"}, {"in": "query", "name": "slug", "type": "string"}, {"in": "query", "name": "prefix", "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-scheduled-events": {"id": "sofascore-scheduled-events", "method": "GET", "params": [{"description": "Numeric SofaScore category id from the categories endpoint", "in": "query", "name": "category_id", "required": true, "type": "string", "x-example": "13"}, {"description": "UTC calendar date, YYYY-MM-DD", "in": "query", "name": "date", "required": true, "type": "string", "x-example": "2026-10-08"}], "path": "/sofascore/scheduled-events", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "category_id", "required": true, "type": "string"}, {"in": "query", "name": "date", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-scheduled-tournaments": {"id": "sofascore-scheduled-tournaments", "method": "GET", "params": [{"description": "Sport key", "enum": ["american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "mma", "minifootball", "padel", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "required": true, "type": "string", "x-example": "football"}, {"description": "UTC calendar date, YYYY-MM-DD", "in": "query", "name": "date", "required": true, "type": "string", "x-example": "2026-10-08"}, {"description": "One-based page number; defaults to 1", "in": "query", "maximum": 100, "minimum": 1, "name": "page", "type": "integer", "x-example": 1}], "path": "/sofascore/scheduled-tournaments", "pathParams": [], "produces": ["application/json"], "queryParams": [{"enum": ["american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "mma", "minifootball", "padel", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "required": true, "type": "string"}, {"in": "query", "name": "date", "required": true, "type": "string"}, {"in": "query", "name": "page", "type": "integer"}], "security": ["ApiKeyAuth"]}, "sofascore-search": {"id": "sofascore-search", "method": "GET", "params": [{"description": "Free-text search query", "in": "query", "name": "q", "required": true, "type": "string", "x-example": "barcelona"}], "path": "/sofascore/search", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "q", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-search-typed": {"id": "sofascore-search-typed", "method": "GET", "params": [{"description": "Entity type to search", "enum": ["events", "teams", "players", "managers", "referees", "venues", "unique_tournaments"], "in": "query", "name": "type", "required": true, "type": "string", "x-example": "teams"}, {"description": "Search text, up to 128 characters", "in": "query", "name": "q", "required": true, "type": "string", "x-example": "arsenal"}, {"description": "Zero-based page number. Defaults to 0", "in": "query", "minimum": 0, "name": "page", "type": "integer", "x-example": 0}, {"description": "Restrict teams, players, managers or unique_tournaments to one sport", "enum": ["american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "mma", "minifootball", "padel", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "type": "string", "x-example": "football"}], "path": "/sofascore/search-typed", "pathParams": [], "produces": ["application/json"], "queryParams": [{"enum": ["events", "teams", "players", "managers", "referees", "venues", "unique_tournaments"], "in": "query", "name": "type", "required": true, "type": "string"}, {"in": "query", "name": "q", "required": true, "type": "string"}, {"in": "query", "name": "page", "type": "integer"}, {"enum": ["american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "mma", "minifootball", "padel", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-season-events": {"id": "sofascore-season-events", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}, {"description": "Upcoming fixtures or finished results", "enum": ["next", "last"], "in": "query", "name": "direction", "required": true, "type": "string", "x-example": "last"}, {"description": "Zero-based page number. Defaults to 0", "in": "query", "minimum": 0, "name": "page", "type": "integer", "x-example": 0}], "path": "/sofascore/season-events", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"enum": ["next", "last"], "in": "query", "name": "direction", "required": true, "type": "string"}, {"in": "query", "name": "page", "type": "integer"}], "security": ["ApiKeyAuth"]}, "sofascore-sports": {"id": "sofascore-sports", "method": "GET", "params": [], "path": "/sofascore/sports", "pathParams": [], "produces": ["application/json"], "queryParams": [], "security": ["ApiKeyAuth"]}, "sofascore-stage": {"id": "sofascore-stage", "method": "GET", "params": [{"description": "Numeric SofaScore stage id from stage-schedule, stage-seasons or stage-substages", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "214258"}], "path": "/sofascore/stage", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-stage-categories": {"id": "sofascore-stage-categories", "method": "GET", "params": [{"description": "Stage sport key", "enum": ["motorsport", "cycling"], "in": "query", "name": "sport", "required": true, "type": "string", "x-example": "motorsport"}], "path": "/sofascore/stage-categories", "pathParams": [], "produces": ["application/json"], "queryParams": [{"enum": ["motorsport", "cycling"], "in": "query", "name": "sport", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-stage-driver-performance": {"id": "sofascore-stage-driver-performance", "method": "GET", "params": [{"description": "Numeric SofaScore stage id of a finished race, sprint or rally", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "214258"}], "path": "/sofascore/stage-driver-performance", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-stage-featured": {"id": "sofascore-stage-featured", "method": "GET", "params": [{"description": "Stage sport key", "enum": ["motorsport", "cycling"], "in": "query", "name": "sport", "required": true, "type": "string", "x-example": "motorsport"}], "path": "/sofascore/stage-featured", "pathParams": [], "produces": ["application/json"], "queryParams": [{"enum": ["motorsport", "cycling"], "in": "query", "name": "sport", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-stage-schedule": {"id": "sofascore-stage-schedule", "method": "GET", "params": [{"description": "Stage sport key", "enum": ["motorsport", "cycling"], "in": "query", "name": "sport", "required": true, "type": "string", "x-example": "motorsport"}, {"description": "UTC calendar date, YYYY-MM-DD", "in": "query", "name": "date", "required": true, "type": "string", "x-example": "2026-10-04"}], "path": "/sofascore/stage-schedule", "pathParams": [], "produces": ["application/json"], "queryParams": [{"enum": ["motorsport", "cycling"], "in": "query", "name": "sport", "required": true, "type": "string"}, {"in": "query", "name": "date", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-stage-seasons": {"id": "sofascore-stage-seasons", "method": "GET", "params": [{"description": "Numeric SofaScore competition id from stage-categories", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "40"}], "path": "/sofascore/stage-seasons", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-stage-standings": {"id": "sofascore-stage-standings", "method": "GET", "params": [{"description": "Numeric SofaScore stage id of a season, event, session or cycling stage", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "214258"}, {"description": "Classification kind", "enum": ["competitor", "team"], "in": "query", "name": "type", "required": true, "type": "string", "x-example": "competitor"}], "path": "/sofascore/stage-standings", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"enum": ["competitor", "team"], "in": "query", "name": "type", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-stage-substages": {"id": "sofascore-stage-substages", "method": "GET", "params": [{"description": "Numeric SofaScore stage id of a season or an event", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "214253"}], "path": "/sofascore/stage-substages", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-standings": {"id": "sofascore-standings", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "76986"}, {"description": "Standings variant", "enum": ["total", "home", "away"], "in": "query", "name": "type", "required": true, "type": "string", "x-example": "total"}], "path": "/sofascore/standings", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"enum": ["total", "home", "away"], "in": "query", "name": "type", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team": {"id": "sofascore-team", "method": "GET", "params": [{"description": "Numeric SofaScore team id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "2817"}], "path": "/sofascore/team", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team-achievements": {"id": "sofascore-team-achievements", "method": "GET", "params": [{"description": "Numeric SofaScore team id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}], "path": "/sofascore/team-achievements", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team-events": {"id": "sofascore-team-events", "method": "GET", "params": [{"description": "Numeric SofaScore team id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "2817"}, {"description": "Fixture direction", "enum": ["next", "last"], "in": "query", "name": "direction", "required": true, "type": "string", "x-example": "next"}, {"description": "Zero-based page number", "in": "query", "name": "page", "type": "integer", "x-example": 0}], "path": "/sofascore/team-events", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"enum": ["next", "last"], "in": "query", "name": "direction", "required": true, "type": "string"}, {"in": "query", "name": "page", "type": "integer"}], "security": ["ApiKeyAuth"]}, "sofascore-team-goal-distributions": {"id": "sofascore-team-goal-distributions", "method": "GET", "params": [{"description": "Numeric SofaScore team id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore unique-tournament (competition) id of a football competition", "in": "query", "name": "tournament_id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}], "path": "/sofascore/team-goal-distributions", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "tournament_id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team-near-events": {"id": "sofascore-team-near-events", "method": "GET", "params": [{"description": "Numeric SofaScore team id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}], "path": "/sofascore/team-near-events", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team-of-the-week": {"id": "sofascore-team-of-the-week", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}, {"description": "Numeric period id from sofascore-team-of-the-week-periods", "in": "query", "name": "period", "required": true, "type": "string", "x-example": "29321"}], "path": "/sofascore/team-of-the-week", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"in": "query", "name": "period", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team-of-the-week-periods": {"id": "sofascore-team-of-the-week-periods", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}], "path": "/sofascore/team-of-the-week-periods", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team-performance": {"id": "sofascore-team-performance", "method": "GET", "params": [{"description": "Numeric SofaScore team id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}], "path": "/sofascore/team-performance", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team-player-statistics": {"id": "sofascore-team-player-statistics", "method": "GET", "params": [{"description": "Numeric SofaScore team id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore unique-tournament (competition) id from team-player-statistics-seasons", "in": "query", "name": "tournament_id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id from team-player-statistics-seasons", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}, {"default": "overall", "description": "Statistics view", "enum": ["overall", "home", "away", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string"}], "path": "/sofascore/team-player-statistics", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "tournament_id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"enum": ["overall", "home", "away", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team-player-statistics-seasons": {"id": "sofascore-team-player-statistics-seasons", "method": "GET", "params": [{"description": "Numeric SofaScore team id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}], "path": "/sofascore/team-player-statistics-seasons", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team-players": {"id": "sofascore-team-players", "method": "GET", "params": [{"description": "Numeric SofaScore team id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "2817"}], "path": "/sofascore/team-players", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team-rankings": {"id": "sofascore-team-rankings", "method": "GET", "params": [{"description": "Numeric SofaScore team id (a tennis player id for tennis)", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "112783"}], "path": "/sofascore/team-rankings", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team-season-statistics": {"id": "sofascore-team-season-statistics", "method": "GET", "params": [{"description": "Numeric SofaScore team id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore unique-tournament (competition) id from team-statistics-seasons", "in": "query", "name": "tournament_id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id from team-statistics-seasons", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}, {"default": "overall", "description": "Statistics view", "enum": ["overall", "home", "away", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string"}], "path": "/sofascore/team-season-statistics", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "tournament_id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"enum": ["overall", "home", "away", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team-statistics-seasons": {"id": "sofascore-team-statistics-seasons", "method": "GET", "params": [{"description": "Numeric SofaScore team id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}], "path": "/sofascore/team-statistics-seasons", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-team-top-players": {"id": "sofascore-team-top-players", "method": "GET", "params": [{"description": "Numeric SofaScore team id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore unique-tournament (competition) id from team-player-statistics-seasons", "in": "query", "name": "tournament_id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id from team-player-statistics-seasons", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}, {"default": "overall", "description": "Statistics scope", "enum": ["overall", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string"}, {"description": "Rows per category, 1 to 50. Omit to return every row", "in": "query", "maximum": 50, "minimum": 1, "name": "limit", "type": "integer", "x-example": 5}], "path": "/sofascore/team-top-players", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "tournament_id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"enum": ["overall", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string"}, {"in": "query", "name": "limit", "type": "integer"}], "security": ["ApiKeyAuth"]}, "sofascore-team-tournaments": {"id": "sofascore-team-tournaments", "method": "GET", "params": [{"description": "Numeric SofaScore team id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"default": false, "description": "false returns current competitions, true returns every recorded competition", "in": "query", "name": "all", "type": "boolean"}], "path": "/sofascore/team-tournaments", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "all", "type": "boolean"}], "security": ["ApiKeyAuth"]}, "sofascore-team-transfers": {"id": "sofascore-team-transfers", "method": "GET", "params": [{"description": "Numeric SofaScore team id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}], "path": "/sofascore/team-transfers", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-tennis-player-grand-slam-results": {"id": "sofascore-tennis-player-grand-slam-results", "method": "GET", "params": [{"description": "Numeric SofaScore team id of a tennis player", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "14882"}], "path": "/sofascore/tennis-player-grand-slam-results", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-tournament-cuptree": {"id": "sofascore-tournament-cuptree", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id of a cup or playoff competition", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "7"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "76953"}], "path": "/sofascore/tournament-cuptree", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-tournament-info": {"id": "sofascore-tournament-info", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id. Omit for competition metadata only", "in": "query", "name": "season", "type": "string", "x-example": "96668"}], "path": "/sofascore/tournament-info", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-tournament-player-of-the-season": {"id": "sofascore-tournament-player-of-the-season", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id of a finished season", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "76986"}], "path": "/sofascore/tournament-player-of-the-season", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-tournament-player-statistics": {"id": "sofascore-tournament-player-statistics", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id of a football competition", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}, {"description": "Statistic to sort by. Defaults to rating", "enum": ["rating", "goals", "expectedGoals", "assists", "successfulDribbles", "tackles", "accuratePassesPercentage", "bigChancesMissed", "totalShots", "goalConversionPercentage", "interceptions", "clearances", "errorLeadToGoal", "outfielderBlocks", "bigChancesCreated", "accuratePasses", "keyPasses", "saves", "cleanSheet", "penaltySave", "savedShotsFromInsideTheBox", "runsOut"], "in": "query", "name": "order", "type": "string", "x-example": "goals"}, {"description": "Sort direction. Defaults to desc", "enum": ["desc", "asc"], "in": "query", "name": "direction", "type": "string", "x-example": "desc"}, {"description": "How statistics are accumulated. Defaults to total", "enum": ["total", "perGame", "per90"], "in": "query", "name": "accumulation", "type": "string", "x-example": "total"}, {"description": "Statistic columns returned. Defaults to summary", "enum": ["summary", "attack", "defence", "passing", "goalkeeper"], "in": "query", "name": "group", "type": "string", "x-example": "summary"}, {"description": "Rows per page, 1 to 100. Defaults to 20", "in": "query", "maximum": 100, "minimum": 1, "name": "limit", "type": "integer", "x-example": 20}, {"description": "Rows to skip. Defaults to 0", "in": "query", "minimum": 0, "name": "offset", "type": "integer", "x-example": 0}, {"collectionFormat": "csv", "description": "Team ids to keep, comma separated, up to 20 (ids from tournament-statistics-info or tournament-teams)", "in": "query", "items": {"type": "string"}, "name": "team", "type": "array", "x-example": "42"}, {"collectionFormat": "csv", "description": "Nationality codes to keep, comma separated, up to 20 (codes from tournament-statistics-info)", "in": "query", "items": {"type": "string"}, "name": "nationality", "type": "array", "x-example": "EN"}, {"collectionFormat": "csv", "description": "Position codes to keep, comma separated", "in": "query", "items": {"enum": ["G", "D", "M", "F"], "type": "string"}, "name": "position", "type": "array", "x-example": "F"}, {"description": "Keep players with at least this many appearances, 0 to 1000. Omit for no minimum", "in": "query", "maximum": 1000, "minimum": 0, "name": "min_appearances", "type": "integer", "x-example": 10}, {"description": "Keep players with at least this many minutes played, 0 to 100000. Omit for no minimum", "in": "query", "maximum": 100000, "minimum": 0, "name": "min_minutes", "type": "integer", "x-example": 900}], "path": "/sofascore/tournament-player-statistics", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"enum": ["rating", "goals", "expectedGoals", "assists", "successfulDribbles", "tackles", "accuratePassesPercentage", "bigChancesMissed", "totalShots", "goalConversionPercentage", "interceptions", "clearances", "errorLeadToGoal", "outfielderBlocks", "bigChancesCreated", "accuratePasses", "keyPasses", "saves", "cleanSheet", "penaltySave", "savedShotsFromInsideTheBox", "runsOut"], "in": "query", "name": "order", "type": "string"}, {"enum": ["desc", "asc"], "in": "query", "name": "direction", "type": "string"}, {"enum": ["total", "perGame", "per90"], "in": "query", "name": "accumulation", "type": "string"}, {"enum": ["summary", "attack", "defence", "passing", "goalkeeper"], "in": "query", "name": "group", "type": "string"}, {"in": "query", "name": "limit", "type": "integer"}, {"in": "query", "name": "offset", "type": "integer"}, {"collectionFormat": "csv", "in": "query", "name": "team", "type": "array"}, {"collectionFormat": "csv", "in": "query", "name": "nationality", "type": "array"}, {"collectionFormat": "csv", "enum": ["G", "D", "M", "F"], "in": "query", "name": "position", "type": "array"}, {"in": "query", "name": "min_appearances", "type": "integer"}, {"in": "query", "name": "min_minutes", "type": "integer"}], "security": ["ApiKeyAuth"]}, "sofascore-tournament-rounds": {"id": "sofascore-tournament-rounds", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "7"}, {"description": "Numeric SofaScore season id from tournament-seasons", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "76953"}], "path": "/sofascore/tournament-rounds", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-tournament-seasons": {"id": "sofascore-tournament-seasons", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}], "path": "/sofascore/tournament-seasons", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-tournament-statistics-info": {"id": "sofascore-tournament-statistics-info", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id of a football competition", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}], "path": "/sofascore/tournament-statistics-info", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-tournament-team-of-the-season": {"id": "sofascore-tournament-team-of-the-season", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id of a finished season", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "76986"}], "path": "/sofascore/tournament-team-of-the-season", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-tournament-teams": {"id": "sofascore-tournament-teams", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}], "path": "/sofascore/tournament-teams", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-tournament-top-players": {"id": "sofascore-tournament-top-players", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}, {"description": "Statistics scope. Defaults to overall", "enum": ["overall", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string", "x-example": "overall"}, {"description": "Rows per category, 1 to 50. Omit to return every row", "in": "query", "maximum": 50, "minimum": 1, "name": "limit", "type": "integer", "x-example": 5}], "path": "/sofascore/tournament-top-players", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"enum": ["overall", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string"}, {"in": "query", "name": "limit", "type": "integer"}], "security": ["ApiKeyAuth"]}, "sofascore-tournament-top-teams": {"id": "sofascore-tournament-top-teams", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}, {"description": "Statistics scope. Defaults to overall", "enum": ["overall", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string", "x-example": "overall"}, {"description": "Rows per category, 1 to 50. Omit to return every row", "in": "query", "maximum": 50, "minimum": 1, "name": "limit", "type": "integer", "x-example": 5}], "path": "/sofascore/tournament-top-teams", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}, {"enum": ["overall", "regular_season", "playoffs"], "in": "query", "name": "type", "type": "string"}, {"in": "query", "name": "limit", "type": "integer"}], "security": ["ApiKeyAuth"]}, "sofascore-tournament-venues": {"id": "sofascore-tournament-venues", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Numeric SofaScore season id", "in": "query", "name": "season", "required": true, "type": "string", "x-example": "96668"}], "path": "/sofascore/tournament-venues", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "season", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-tournament-winners": {"id": "sofascore-tournament-winners", "method": "GET", "params": [{"description": "Numeric SofaScore unique-tournament (competition) id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "17"}, {"description": "Zero-based page number. Defaults to 0", "in": "query", "minimum": 0, "name": "page", "type": "integer", "x-example": 0}], "path": "/sofascore/tournament-winners", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"in": "query", "name": "page", "type": "integer"}], "security": ["ApiKeyAuth"]}, "sofascore-tournaments-with-feature": {"id": "sofascore-tournaments-with-feature", "method": "GET", "params": [{"description": "Competition feature", "enum": ["cuptree", "standings", "totw", "power_rankings"], "in": "query", "name": "feature", "required": true, "type": "string", "x-example": "cuptree"}, {"description": "Sport key", "enum": ["american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "minifootball", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "required": true, "type": "string", "x-example": "football"}], "path": "/sofascore/tournaments-with-feature", "pathParams": [], "produces": ["application/json"], "queryParams": [{"enum": ["cuptree", "standings", "totw", "power_rankings"], "in": "query", "name": "feature", "required": true, "type": "string"}, {"enum": ["american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "minifootball", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-trending-events": {"id": "sofascore-trending-events", "method": "GET", "params": [{"description": "Two-letter ISO 3166-1 alpha-2 country code", "in": "query", "name": "country", "required": true, "type": "string", "x-example": "GB"}], "path": "/sofascore/trending-events", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "country", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-trending-players": {"id": "sofascore-trending-players", "method": "GET", "params": [{"description": "Sport key", "enum": ["football", "basketball"], "in": "query", "name": "sport", "required": true, "type": "string", "x-example": "football"}], "path": "/sofascore/trending-players", "pathParams": [], "produces": ["application/json"], "queryParams": [{"enum": ["football", "basketball"], "in": "query", "name": "sport", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-venue": {"id": "sofascore-venue", "method": "GET", "params": [{"description": "Numeric SofaScore venue id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "624"}], "path": "/sofascore/venue", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}], "security": ["ApiKeyAuth"]}, "sofascore-venue-events": {"id": "sofascore-venue-events", "method": "GET", "params": [{"description": "Numeric SofaScore venue id", "in": "query", "name": "id", "required": true, "type": "string", "x-example": "624"}, {"description": "Upcoming matches or finished matches", "enum": ["next", "last"], "in": "query", "name": "direction", "required": true, "type": "string", "x-example": "last"}, {"description": "Sport filter for the venue-wide list. Defaults to all", "enum": ["all", "american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "mma", "minifootball", "padel", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "type": "string", "x-example": "football"}, {"description": "Zero-based page number. Defaults to 0", "in": "query", "minimum": 0, "name": "page", "type": "integer", "x-example": 0}, {"description": "Numeric unique-tournament id; with season, lists that competition season's matches at the venue", "in": "query", "name": "tournament", "type": "string", "x-example": "17"}, {"description": "Numeric season id; with tournament, lists that competition season's matches at the venue", "in": "query", "name": "season", "type": "string", "x-example": "96668"}], "path": "/sofascore/venue-events", "pathParams": [], "produces": ["application/json"], "queryParams": [{"in": "query", "name": "id", "required": true, "type": "string"}, {"enum": ["next", "last"], "in": "query", "name": "direction", "required": true, "type": "string"}, {"enum": ["all", "american-football", "aussie-rules", "badminton", "bandy", "baseball", "basketball", "beach-volley", "cricket", "darts", "esports", "floorball", "football", "futsal", "handball", "ice-hockey", "mma", "minifootball", "padel", "rugby", "snooker", "table-tennis", "tennis", "volleyball", "waterpolo"], "in": "query", "name": "sport", "type": "string"}, {"in": "query", "name": "page", "type": "integer"}, {"in": "query", "name": "tournament", "type": "string"}, {"in": "query", "name": "season", "type": "string"}], "security": ["ApiKeyAuth"]}}
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
            'User-Agent: crawlora-sofascore-php/0.3.3',
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
    public function draft(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-draft", $params, $responseType);
    }
    public function draft_picks(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-draft-picks", $params, $responseType);
    }
    public function esports_game(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-esports-game", $params, $responseType);
    }
    public function event(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event", $params, $responseType);
    }
    public function event_at_bat_pitches(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-at-bat-pitches", $params, $responseType);
    }
    public function event_at_bats(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-at-bats", $params, $responseType);
    }
    public function event_average_positions(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-average-positions", $params, $responseType);
    }
    public function event_baseball_top_performers(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-baseball-top-performers", $params, $responseType);
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
    public function event_esports_games(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-esports-games", $params, $responseType);
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
    public function event_highlights(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-highlights", $params, $responseType);
    }
    public function event_incidents(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-incidents", $params, $responseType);
    }
    public function event_innings(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-innings", $params, $responseType);
    }
    public function event_lineups(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-lineups", $params, $responseType);
    }
    public function event_managers(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-managers", $params, $responseType);
    }
    public function event_odds(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-odds", $params, $responseType);
    }
    public function event_player_heatmap(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-player-heatmap", $params, $responseType);
    }
    public function event_player_statistics(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-player-statistics", $params, $responseType);
    }
    public function event_point_by_point(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-point-by-point", $params, $responseType);
    }
    public function event_pregame_form(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-pregame-form", $params, $responseType);
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
    public function event_team_heatmap(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-team-heatmap", $params, $responseType);
    }
    public function event_team_streaks(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-team-streaks", $params, $responseType);
    }
    public function event_tennis_power(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-tennis-power", $params, $responseType);
    }
    public function event_tv_channels(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-tv-channels", $params, $responseType);
    }
    public function event_votes(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-event-votes", $params, $responseType);
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
    public function mma_card(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-mma-card", $params, $responseType);
    }
    public function mma_schedule(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-mma-schedule", $params, $responseType);
    }
    public function odds_dropping(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-odds-dropping", $params, $responseType);
    }
    public function odds_winning(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-odds-winning", $params, $responseType);
    }
    public function player(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-player", $params, $responseType);
    }
    public function player_attributes(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-player-attributes", $params, $responseType);
    }
    public function player_events(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-player-events", $params, $responseType);
    }
    public function player_last_year_summary(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-player-last-year-summary", $params, $responseType);
    }
    public function player_national_team_statistics(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-player-national-team-statistics", $params, $responseType);
    }
    public function player_penalty_history(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-player-penalty-history", $params, $responseType);
    }
    public function player_ratings(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-player-ratings", $params, $responseType);
    }
    public function player_season_heatmap(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-player-season-heatmap", $params, $responseType);
    }
    public function player_season_statistics(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-player-season-statistics", $params, $responseType);
    }
    public function player_statistical_rankings(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-player-statistical-rankings", $params, $responseType);
    }
    public function player_statistics_seasons(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-player-statistics-seasons", $params, $responseType);
    }
    public function player_tournaments(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-player-tournaments", $params, $responseType);
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
    public function referee(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-referee", $params, $responseType);
    }
    public function referee_events(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-referee-events", $params, $responseType);
    }
    public function referee_statistics(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-referee-statistics", $params, $responseType);
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
    public function search_typed(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-search-typed", $params, $responseType);
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
    public function stage(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-stage", $params, $responseType);
    }
    public function stage_categories(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-stage-categories", $params, $responseType);
    }
    public function stage_driver_performance(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-stage-driver-performance", $params, $responseType);
    }
    public function stage_featured(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-stage-featured", $params, $responseType);
    }
    public function stage_schedule(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-stage-schedule", $params, $responseType);
    }
    public function stage_seasons(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-stage-seasons", $params, $responseType);
    }
    public function stage_standings(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-stage-standings", $params, $responseType);
    }
    public function stage_substages(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-stage-substages", $params, $responseType);
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
    public function team_achievements(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-team-achievements", $params, $responseType);
    }
    public function team_events(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-team-events", $params, $responseType);
    }
    public function team_goal_distributions(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-team-goal-distributions", $params, $responseType);
    }
    public function team_near_events(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-team-near-events", $params, $responseType);
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
    public function team_performance(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-team-performance", $params, $responseType);
    }
    public function team_player_statistics(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-team-player-statistics", $params, $responseType);
    }
    public function team_player_statistics_seasons(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-team-player-statistics-seasons", $params, $responseType);
    }
    public function team_players(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-team-players", $params, $responseType);
    }
    public function team_rankings(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-team-rankings", $params, $responseType);
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
    public function team_top_players(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-team-top-players", $params, $responseType);
    }
    public function team_tournaments(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-team-tournaments", $params, $responseType);
    }
    public function team_transfers(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-team-transfers", $params, $responseType);
    }
    public function tennis_player_grand_slam_results(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-tennis-player-grand-slam-results", $params, $responseType);
    }
    public function tournament_cuptree(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-tournament-cuptree", $params, $responseType);
    }
    public function tournament_info(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-tournament-info", $params, $responseType);
    }
    public function tournament_player_of_the_season(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-tournament-player-of-the-season", $params, $responseType);
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
    public function tournament_statistics_info(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-tournament-statistics-info", $params, $responseType);
    }
    public function tournament_team_of_the_season(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-tournament-team-of-the-season", $params, $responseType);
    }
    public function tournament_teams(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-tournament-teams", $params, $responseType);
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
    public function tournament_venues(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-tournament-venues", $params, $responseType);
    }
    public function tournament_winners(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-tournament-winners", $params, $responseType);
    }
    public function tournaments_with_feature(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-tournaments-with-feature", $params, $responseType);
    }
    public function trending_events(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-trending-events", $params, $responseType);
    }
    public function trending_players(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-trending-players", $params, $responseType);
    }
    public function venue(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-venue", $params, $responseType);
    }
    public function venue_events(mixed ...$params): mixed
    {
        $responseType = $params['_response_type'] ?? $params['response_type'] ?? 'auto';
        unset($params['_response_type'], $params['response_type']);
        return $this->request("sofascore-venue-events", $params, $responseType);
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
