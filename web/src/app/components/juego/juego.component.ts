import { CommonModule } from '@angular/common';
import { ChangeDetectionStrategy, Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
(window as any).Pusher = Pusher


@Component({
  selector: 'app-juego',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './juego.component.html',
  styleUrl: './juego.component.css',
  changeDetection: ChangeDetectionStrategy.OnPush
})
export class JuegoComponent implements OnInit {

  board: string[][] = [];



  constructor() { }
  
  public echo: Echo = new Echo({
    broadcaster:'pusher',
    key:'123',
    cluster:'mt1',
    wsHost:'localhost',
    wsPort:6001,
    forceTLS:false,
    disableStatus:true,
  })
  ngOnInit(): void {
    // this.echo.channel('game-events')
    // .listen('TurnChanged', (event: any) => {
    //   // Manejar el evento de cambio de turno
    //   console.log('Turno cambiado:', event);
    // })
    // .listen('GameUpdated', (event: any) => {
    //   // Manejar el evento de actualización del juego (por ejemplo, actualización del tablero)
    //   console.log('Juego actualizado:', event);
    // })
    this.generateBoard();
  }
  trackByIndex(index: number, item: any): number {
    return index;
  }


    
  generateBoard(): void {
    const numRows = 8;
    const numCols = 5;
    const greenCells = 15; // Número de celdas que deben estar en verde

    // Inicializar el tablero con todas las celdas en azul
    this.board = Array(numRows)
      .fill(null)
      .map(() => Array(numCols).fill('#13E5F7'));

    const positions = this.generateRandomPositions(numRows, numCols, greenCells);

    positions.forEach(pos => {
      this.board[pos.row][pos.col] = '#C7C8C8';
    });
  }

  generateRandomPositions(numRows: number, numCols: number, count: number): { row: number, col: number }[] {
    const positions: { row: number, col: number }[] = [];
    const totalCells = numRows * numCols;

    // Generar un conjunto único de posiciones aleatorias
    while (positions.length < count) {
      const randomIndex = Math.floor(Math.random() * totalCells);
      const row = Math.floor(randomIndex / numCols);
      const col = randomIndex % numCols;
      if (!positions.some(pos => pos.row === row && pos.col === col)) {
        positions.push({ row, col });
      }
    }

    return positions;
  }

  sendBoardPosition(row: number, col: number) {
    const position = { row, col };
    console.log(position)
    // this.http.post('http://tu-api.com/game/' + gameId + '/make-move', position)
    //   .subscribe(
    //     (response) => {
    //       console.log('Respuesta del servidor:', response);
    //       // Manejar la respuesta del servidor si es necesario
    //     },
    //     (error) => {
    //       console.error('Error al enviar la posición del tablero:', error);
    //       // Manejar el error si es necesario
    //     }
    //   );
  }

}
