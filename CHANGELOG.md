## [2.x](https://github.com/comexio/php-utils/compare/1.x...2.x) - 2022-10-03

### About

A versão 2.x foi criada para conseguir juntar a 0.x e 1.x em um caminho só.
Ela foi feita em cima da 0.x adequando a mesma para trabalhar com no mínimo Laravel/Lumen 8.x e php 7.4 ou 8.1.
Assim essa paralelização que foi criada em cima de versões agora pode ser retornada a somente uma branche de produção.

### Changed

- Docker alterado para trabalhar com php 8.1 e php 7.4
- Pipelines alteradas para trabalhar com php 8.1
- Alterações de código referentes a atualização do php 8 e lumen 9.x
- Correções de código referente as breaking changes da nova versão do lumen

## [1.x](https://github.com/comexio/php-utils/compare/0.x...1.x) - 2021-02-08

### About

A versão 0.x foi adequada para trabalhar com o php 8, e para isso foi lançado a 1.x.
Ambas deveriam trabalhar em paralelo até que a 0.x (php 7) fosse descontinuado nas aplicações que utilizam o pacote.
Porém isso não ocorreu, muitas alterações feitas na 0.x não foram passadas para a 1.x, e hoje poucas apis utilizam também essa versão.
Com isso a mesma está hoje depreciada.

### Changed

- Docker alterado para trabalhar com php 8
- Pipelines alteradas para trabalhar com php 8
- Alterações de código referentes a atualização do php 8 e lumen 9.x

## [0.x](https://github.com/comexio/php-utils/tree/0.x) - 2020-08-12

### About

Lançado primeira versão do pacote