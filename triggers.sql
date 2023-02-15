-- AL INSERTAR
CREATE TRIGGER actualizarDisponible_AI AFTER INSERT ON gastos FOR EACH ROW UPDATE disponible SET disponible.cantidad=NEW.total WHERE id=1;

-- AL ACTUALIZAR
CREATE TRIGGER actDisponible_AU AFTER UPDATE ON gastos FOR EACH ROW UPDATE disponible SET disponible.cantidad=NEW.total WHERE id=1;

DROP TRIGGER IF EXISTS actualizarDisponible_AI;
DROP TRIGGER IF EXISTS actDisponible_AU;