<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string|null $nombre
 * @property string $codigo_alumno
 * @property int|null $materia_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Materia|null $materia
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlumnoAyudantia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlumnoAyudantia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlumnoAyudantia query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlumnoAyudantia whereCodigoAlumno($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlumnoAyudantia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlumnoAyudantia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlumnoAyudantia whereMateriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlumnoAyudantia whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AlumnoAyudantia whereUpdatedAt($value)
 */
	class AlumnoAyudantia extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AsignacionBeca> $asignacionesBecas
 * @property-read int|null $asignaciones_becas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\JefeDeArea> $jefesDeArea
 * @property-read int|null $jefes_de_area_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Area newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Area newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Area query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Area whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Area whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Area whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Area whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Area whereUpdatedAt($value)
 */
	class Area extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $becario_id
 * @property int $beca_id
 * @property int $gestion_id
 * @property int $area_id
 * @property int $jefe_area_id
 * @property int|null $materia_id
 * @property int $porcentaje_obtenido
 * @property int $horas_acumuladas
 * @property string $estado
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Area $area
 * @property-read \App\Models\Beca $beca
 * @property-read \App\Models\Becario $becario
 * @property-read \App\Models\Gestion $gestion
 * @property-read \App\Models\JefeDeArea $jefeArea
 * @property-read \App\Models\Materia|null $materia
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionBeca newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionBeca newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionBeca query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionBeca whereAreaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionBeca whereBecaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionBeca whereBecarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionBeca whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionBeca whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionBeca whereGestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionBeca whereHorasAcumuladas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionBeca whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionBeca whereJefeAreaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionBeca whereMateriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionBeca wherePorcentajeObtenido($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsignacionBeca whereUpdatedAt($value)
 */
	class AsignacionBeca extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $registro_asistencia_ayudantia_id
 * @property int $alumno_ayudantia_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AlumnoAyudantia $alumno
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenteAyudantia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenteAyudantia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenteAyudantia query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenteAyudantia whereAlumnoAyudantiaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenteAyudantia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenteAyudantia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenteAyudantia whereRegistroAsistenciaAyudantiaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AsistenteAyudantia whereUpdatedAt($value)
 */
	class AsistenteAyudantia extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property string|null $descripcion
 * @property int|null $horas_requeridas
 * @property int|null $porcentaje_beca
 * @property string $tipo_beca
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Beca newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Beca newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Beca query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Beca whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Beca whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Beca whereHorasRequeridas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Beca whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Beca whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Beca wherePorcentajeBeca($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Beca whereTipoBeca($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Beca whereUpdatedAt($value)
 */
	class Beca extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $carrera_id
 * @property string $codigo_estudiante
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AsignacionBeca> $asignaciones
 * @property-read int|null $asignaciones_count
 * @property-read \App\Models\Carrera $carrera
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Becario newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Becario newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Becario query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Becario whereCarreraId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Becario whereCodigoEstudiante($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Becario whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Becario whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Becario whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Becario whereUserId($value)
 */
	class Becario extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property string|null $descripcion
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Becario> $becarios
 * @property-read int|null $becarios_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrera newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrera newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrera query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrera whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrera whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrera whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrera whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Carrera whereUpdatedAt($value)
 */
	class Carrera extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nombre
 * @property \Illuminate\Support\Carbon $fecha_inicio
 * @property \Illuminate\Support\Carbon $fecha_fin
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion whereFechaFin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion whereFechaInicio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gestion whereUpdatedAt($value)
 */
	class Gestion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int|null $area_id
 * @property string|null $cargo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Area|null $area
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AsignacionBeca> $asignacionesBecas
 * @property-read int|null $asignaciones_becas_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JefeDeArea newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JefeDeArea newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JefeDeArea query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JefeDeArea whereAreaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JefeDeArea whereCargo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JefeDeArea whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JefeDeArea whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JefeDeArea whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JefeDeArea whereUserId($value)
 */
	class JefeDeArea extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $carrera_id
 * @property string $nombre
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Carrera $carrera
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Materia> $materias
 * @property-read int|null $materias_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Materia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Materia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Materia query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Materia whereCarreraId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Materia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Materia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Materia whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Materia whereUpdatedAt($value)
 */
	class Materia extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $asignacion_beca_id
 * @property int|null $validado_por
 * @property \Illuminate\Support\Carbon $fecha
 * @property \Illuminate\Support\Carbon $hora_entrada
 * @property \Illuminate\Support\Carbon|null $hora_salida
 * @property numeric|null $total_horas
 * @property string|null $actividad_principal
 * @property string|null $motivo_rechazo
 * @property bool $verificado_facial
 * @property numeric|null $confidence_score
 * @property string $estado
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AsignacionBeca $asignacionBeca
 * @property-read \App\Models\RegistroAsistenciaAyudantia|null $ayudantia
 * @property-read \App\Models\User|null $validadoPor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistencia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistencia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistencia query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistencia whereActividadPrincipal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistencia whereAsignacionBecaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistencia whereConfidenceScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistencia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistencia whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistencia whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistencia whereHoraEntrada($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistencia whereHoraSalida($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistencia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistencia whereMotivoRechazo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistencia whereTotalHoras($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistencia whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistencia whereValidadoPor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistencia whereVerificadoFacial($value)
 */
	class RegistroAsistencia extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $registro_asistencia_id
 * @property string $foto_clase
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AsistenteAyudantia> $asistentes
 * @property-read int|null $asistentes_count
 * @property-read \App\Models\RegistroAsistencia $registro
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistenciaAyudantia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistenciaAyudantia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistenciaAyudantia query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistenciaAyudantia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistenciaAyudantia whereFotoClase($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistenciaAyudantia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistenciaAyudantia whereRegistroAsistenciaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RegistroAsistenciaAyudantia whereUpdatedAt($value)
 */
	class RegistroAsistenciaAyudantia extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property array<array-key, mixed> $descriptor
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rostro newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rostro newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rostro query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rostro whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rostro whereDescriptor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rostro whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rostro whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Rostro whereUserId($value)
 */
	class Rostro extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property int $activo
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Becario|null $becario
 * @property-read \App\Models\JefeDeArea|null $jefeDeArea
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\Rostro|null $rostro
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, ?string $guard = null, bool $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereActivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, ?string $guard = null)
 */
	class User extends \Eloquent implements \Filament\Models\Contracts\FilamentUser {}
}

