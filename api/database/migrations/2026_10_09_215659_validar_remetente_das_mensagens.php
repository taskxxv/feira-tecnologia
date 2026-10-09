
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION validar_remetente_conversa()
            RETURNS TRIGGER AS $$
            BEGIN
                IF NOT EXISTS (
                    SELECT 1
                    FROM conversas
                    WHERE id = NEW.conversa_id
                      AND NEW.remetente_id IN (
                          usuario_1_id,
                          usuario_2_id
                      )
                ) THEN
                    RAISE EXCEPTION
                        'O remetente não participa desta conversa';
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER trigger_validar_remetente
            BEFORE INSERT OR UPDATE
            ON mensagens
            FOR EACH ROW
            EXECUTE FUNCTION validar_remetente_conversa();
        SQL);
    }

    public function down(): void
    {
        DB::unprepared(<<<'SQL'
            DROP TRIGGER IF EXISTS
                trigger_validar_remetente ON mensagens;

            DROP FUNCTION IF EXISTS
                validar_remetente_conversa();
        SQL);
    }
};
