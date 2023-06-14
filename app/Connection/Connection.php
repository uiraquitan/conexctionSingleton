<?php

namespace App\Connection;

use Exception;
use PDO;

final class Connection
{
    private static $connection;

    /**
     * Singleton: Método construtor privado para impedir classes
     * de gerar instâncias
     */

    private function __construct()
    {
    }

    public static function getInstance(string $arquivo)
    {
        if (self::$connection === null) {
            self::$connection = self::make(self::load($arquivo));
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$connection->exec("set names utf8");
        } else {
            throw new Exception("Error Processing Request", 1);
        }
        return self::$connection;
    }

    /**
     * Método resposável por ler o arquivo .ini
     *
     * @param string $arquivo
     * @return array
     */
    private static function load(string $arquivo): array
    {

        if (file_exists($arquivo)) {
            $dados = parse_ini_file($arquivo);
            var_dump($dados);
        } else {
            throw new Exception('Erro: Arquivo não encontrado');
        }
        return $dados;
    }

    /**
     * Método montar string de conexao e gerar o objeto PDO
     * @param $dados array
     * @return PDO
     */

    private static function make(array $dados): PDO
    {
        // capturar dados
        $sgdb     = isset($dados['sgdb']) ? $dados['sgdb'] : NULL;
        $usuario  = isset($dados['usuario']) ? $dados['usuario'] : NULL;
        $senha    = isset($dados['senha']) ? $dados['senha'] : NULL;
        $banco    = isset($dados['banco']) ? $dados['banco'] : NULL;
        $servidor = isset($dados['servidor']) ? $dados['servidor'] : NULL;
        $porta    = isset($dados['porta']) ? $dados['porta'] : NULL;

        if (!is_null($sgdb)) {
            // selecionar banco - criar string de conexão
            switch (strtoupper($sgdb)) {
                case 'MYSQL':
                    $porta = isset($porta) ? $porta : 3306;
                    return new PDO("mysql:host={$servidor};port={$porta};dbname={$banco}", $usuario, $senha);
                    break;
                case 'MSSQL':
                    $porta = isset($porta) ? $porta : 1433;
                    return new PDO("mssql:host={$servidor},{$porta};dbname={$banco}", $usuario, $senha);
                    break;
                case 'PGSQL':
                    $porta = isset($porta) ? $porta : 5432;
                    return new PDO("pgsql:dbname={$banco}; user={$usuario}; password={$senha}, host={$servidor};port={$porta}");
                    break;
                case 'SQLITE':
                    return new PDO("sqlite:{$banco}");
                    break;
                case 'OCI8':
                    return new PDO("oci:dbname={$banco}", $usuario, $senha);
                    break;
                case 'FIREBIRD':
                    return new PDO("firebird:dbname={$banco}", $usuario, $senha);
                    break;
            }
        } else {
            throw new Exception('Erro: tipo de banco de dados não informado');
        }
    }
}
