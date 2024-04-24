import { CommonModule } from '@angular/common';
import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { JuegoService } from '../../services/juego.service';
import { JuegoActivo } from '../../Interfaces/juego-activo';
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
  oponentBoard: string[][] = [];

  echo: Echo = new Echo({
    broadcaster:'pusher',
    key:'123',
    cluster:'mt1',
    wsHost:'192.168.100.224',
    wsPort:6001,
    forceTLS:false,
    disableStatus:true,
  })

  constructor(private js:JuegoService, private cdr: ChangeDetectorRef) { }
  

  public juego: JuegoActivo = {
    game: {
      id: 0,
      next_player_id: 0,
      player1_id: 0,
      player2_id: 0,
      status: "",
      winner_id: 0,
    }
  }

  public barcosRival: number = 0
  public barcosUsuario: number = 0
  


  ngOnInit(): void {
    this.generateBoard();
    this.generateOponentBoard();
    const savedPositions = JSON.parse(localStorage.getItem('positions') || '[]');
  
    // Obtener la información del juego
    this.js.getPartida().subscribe(
      (response) => {
        // Asignar el ID del juego
        this.juego.game.id = response.game.id;
        this.js.colocarBarcos(savedPositions, this.juego.game.id).subscribe(
          (response) => {
            // Manejar la respuesta si es necesario
          },
          (error) => {
            console.error('Error al colocar barcos:', error);
          }
        );
  
      },
      (error) => {
        console.error('Error al obtener información del juego:', error);
      }
    );
  
    // Establecer la conexión WebSocket una vez que se inicializa el componente
    this.websocket();
  }
  
  sendBoardPosition(vertical: number, horizontal: number) {
    const position = { vertical, horizontal };
  
    console.log(this.barcosRival)
    console.log(position)
  
    console.log(this.juego.game.id)
  
    this.js.movimiento(horizontal, vertical, this.juego.game.id).subscribe(
      (response) => {
        console.log(response)
      }
    )
  }

  trackByIndex(index: number, item: any): number {
    return index;
  }

 
  websocket() {
    this.echo.channel('barcos.' + this.juego.game.id)
      .listen('.BarcoEvents', (data: any) => {
        console.log(typeof data.barcosRival)
        this.barcosRival = data.barcosRival;
        this.barcosUsuario = data.barcosUsuario;
        this.cdr.detectChanges(); // Forzar la detección de cambios
      });
    this.echo.connect();
  }

  generateBoard(): void {
    const numCols = 5;
    const numRows = 8;
    const greenCells = 15; 

    this.board = Array(numRows)
      .fill(null)
      .map(() => Array(numCols).fill('#13E5F7'));

    const positions = this.generateRandomPositions(numRows, numCols, greenCells);

    positions.forEach(pos => {
      this.board[pos.vertical][pos.horizontal] = '#C7C8C8';
    });
  }

  generateRandomPositions(numRows: number, numCols: number, count: number): { vertical: number, horizontal: number }[] {
    let positions: { vertical: number, horizontal: number }[] = [];
    const totalCells = numRows * numCols;
  
    while (positions.length < count) {
      const randomIndex = Math.floor(Math.random() * totalCells);
      const vertical = Math.floor(randomIndex / numCols);
      const horizontal = randomIndex % numCols;
  
      if (!positions.some(pos => pos.vertical === vertical && pos.horizontal === horizontal)) {
        positions.push({ vertical, horizontal });
      }
    }
  
    if (!localStorage.getItem('positions')) {
      localStorage.setItem('positions', JSON.stringify(positions));
    } else {
      const savedPositions = JSON.parse(localStorage.getItem('positions') || '[]');
      positions = savedPositions;
    }
  
    return positions;
  }

  // sendBoardPosition(vertical: number, horizontal: number) {
  //   const position = { vertical, horizontal };

  //   console.log(this.barcosRival)
  //   console.log(position)
  //   this.websocket()

  //   console.log(this.juego.game.id)

  //   this.js.movimiento(horizontal, vertical, this.juego.game.id).subscribe(
  //     (response) => {
  //       console.log(response)
  //     }
  //   )
    
  // }
  generateOponentBoard(): void {
    const numCols = 5;
    const numRows = 8;

    this.oponentBoard = Array(numRows)
      .fill(null)
      .map(() => Array(numCols).fill('#fffff'));
  }

}
