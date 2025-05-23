<?php
namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\SecurityScheme;
use ArrayObject;

final class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated) {}

    public function __invoke(array $context = []): \ApiPlatform\OpenApi\OpenApi
    {
        $openApi = ($this->decorated)($context);

        $components = $openApi->getComponents();
        $components = $components->withSecuritySchemes(new ArrayObject ([
            'bearerAuth' => new SecurityScheme(
                type: 'http',
                scheme: 'bearer',
                bearerFormat: 'JWT',
                description: 'JWT Authorization header using the Bearer scheme. Example: "Bearer {token}"'
            )
        ]));

        $openApi = $openApi->withComponents($components);
        $openApi = $openApi->withSecurity([new ArrayObject(['bearerAuth' => []])]);

        return $openApi;
    }
}