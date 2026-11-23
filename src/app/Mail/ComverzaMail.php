<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Correo genérico con la plantilla de COMVERZA.
 *
 * Se usa para: bienvenida de usuarios, acuse y aviso de contacto,
 * confirmación de pedido (con PDF adjunto), alertas de stock bajo y
 * envío de reportes desde el dashboard.
 */
class ComverzaMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{0:string,1:string}>  $filas  pares etiqueta/valor
     * @param  array<int, array{data:string,nombre:string,mime:string}>  $adjuntos
     */
    public function __construct(
        public string $asunto,
        public string $titulo,
        public string $intro,
        public array $filas = [],
        public ?string $botonTexto = null,
        public ?string $botonUrl = null,
        public ?string $replyToEmail = null,
        public array $adjuntos = [],
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->asunto.' · COMVERZA',
            replyTo: $this->replyToEmail ? [$this->replyToEmail] : [],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.general');
    }

    public function attachments(): array
    {
        return collect($this->adjuntos)
            ->map(fn ($a) => Attachment::fromData(fn () => base64_decode($a['data']), $a['nombre'])->withMime($a['mime']))
            ->all();
    }

    /** Helper para adjuntar un binario (p. ej. un PDF de DomPDF). */
    public static function adjunto(string $binario, string $nombre, string $mime = 'application/pdf'): array
    {
        return ['data' => base64_encode($binario), 'nombre' => $nombre, 'mime' => $mime];
    }
}
