import { CommonModule } from '@angular/common';
import { ChangeDetectionStrategy, Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { JuegoService } from '../../services/juego.service';



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

  echo:any

  constructor(private service:JuegoService) { }
  



  


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
    this.service.getPartida().subscribe(
      (response) => {
        console.log(response); 
      }
    ); 
    this.websocket();
    
    
  }

  trackByIndex(index: number, item: any): number {
    return index;
  }

 
  websocket(){
    (window as any).Pusher = Pusher
    this.echo = new Echo({
      broadcaster:'pusher',
      key:'123',
      cluster:'mt1',
      wsHost:'localhost',
      wsPort:6001,
      forceTLS:false,
      disableStatus:true,
    })
    this.echo.channel('getbarcos').listen('.App\\Events\\BarcoEvents',(e:any) => {
      console.log(e);
    })
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
    const positions: { vertical: number, horizontal: number }[] = [];
    const totalCells = numRows * numCols;
    while (positions.length < count) {
      const randomIndex = Math.floor(Math.random() * totalCells);
      const vertical = Math.floor(randomIndex / numCols);
      const horizontal = randomIndex % numCols;
      if (!positions.some(pos => pos.vertical === vertical && pos.horizontal === horizontal)) {
        positions.push({ vertical, horizontal });
      }
    }

    console.log(positions)

    return positions;
  }

  sendBoardPosition(vertical: number, horizontal: number) {
    const position = { vertical, horizontal };
    console.log(position)
    
  }



}
