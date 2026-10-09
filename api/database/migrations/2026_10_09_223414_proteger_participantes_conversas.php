
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE FUNCTION impedir_alteracao_participantes()
            RETURNS TRIGGER AS $$
            BEGIN
                IF NEW.usuario_1_id IS DISTINCT FROM OLD.usuario_1_id
                   OR NEW.usuario_2_id IS DISTINCT FROM OLD.usuario_2_id
                THEN
                    RAISE EXCEPTION
                        'Os participantes de uma conversa não podem ser alterados';
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;

            CREATE TRIGGER trigger_proteger_participantes
            BEFORE UPDATE OF usuario_1_id, usuario_2_id
            ON conversas
            FOR EACH ROW
            EXECUTE FUNCTION impedir_alteracao_participantes();
        SQL);
    }

    public function down(): void
    {
        DB::unprepared(<<<'SQL'
            DROP TRIGGER IF EXISTS
                trigger_proteger_participantes ON conversas;

            DROP FUNCTION IF EXISTS
                impedir_alteracao_participantes();
        SQL);
    }
};
