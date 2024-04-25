import { ChangeDetectionStrategy, ChangeDetectorRef, Component, OnInit } from '@angular/core';
import { UserService } from '../../services/user.service';
import { JuegoService } from '../../services/juego.service';
import { User } from '../../Interfaces/user-interface';
import { JuegoActivo } from '../../Interfaces/juego-activo';
import Swal from 'sweetalert2';
import Echo from 'laravel-echo';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';
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
  oponentBoard: string[][] = [];
  echo: Echo = new Echo({
    broadcaster:'pusher',
    key:'123',
    cluster:'mt1',
    wsHost:'192.168.0.155',
    wsPort:6001,
    forceTLS:false,
    disableStatus:true,
  })
  public user: User = {
    id: 0,
    email: "",
    name: "",
    codigoVerificadO: false,
    created_at: "",
    is_active: false,
    updated_at: ""
  }
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
  public barcosRival: number = 15
  public barcosUsuario: number = 15
  public player1_id: number = 0
  public player2_id: number = 0

  constructor(
    private us: UserService,
    private js: JuegoService,
    private cdr: ChangeDetectorRef,
    private router: Router
  ) { }

  ngOnInit(): void {
    this.generateBoard();
    this.generateOponentBoard();
    this.websocketPartida();

    
    this.websocketHit();

    const savedPositions = JSON.parse(localStorage.getItem('positions') || '[]');

    this.us.getData().subscribe(
      (response) => {
        this.user = response
      }
    )

    this.js.getPartida().subscribe(
      (response) => {



        this.juego.game.id = response.game.id;
        
        console.log(response)
        this.js.colocarBarcos(savedPositions, this.juego.game.id).subscribe(
          (response) => {

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
    this.js.movimiento(-1, -1, this.juego.game.id).subscribe(
      (response) => {
        console.log(response);
        // if (this.player1_id == this.user.id) {
        //   this.barcosRival -= response.is_successful
        // } else if (this.player2_id == this.user.id) {
        //   this.barcosUsuario -= response.is_successful
        // }

        console.log(this.barcosRival)
        console.log(this.barcosUsuario)

        // if (response.is_successful) {
        //   Swal.fire({
        //     icon: 'success',
        //     title: '¡Le diste!',
        //     text: 'Acabas de derribar un barco enemigo',
        //   });
        // } else if (response.is_successful == false) {
        //   Swal.fire({
        //     icon: 'warning',
        //     title: 'No has atinado a un barco',
        //   });
        // }
        // Marcar para revisión del cambio
      },
      (error) => {
        console.error('Error:', error);
        if (error.error === 'No es tu turno >:(') {
          Swal.fire({
            icon: 'error',
            title: error.error,
          });
        }
      }
    );
  }

  sendBoardPosition(vertical: number, horizontal: number) {
    const position = { vertical, horizontal };
    console.log(this.barcosRival);
    console.log(position);
    console.log(this.juego.game.id);

    this.websocketHit()

    this.js.movimiento(horizontal, vertical, this.juego.game.id).subscribe(
      (response) => {
        console.log(response);
        // if (this.player1_id == this.user.id) {
        //   this.barcosRival -= response.is_successful
        // } else if (this.player2_id == this.user.id) {
        //   this.barcosUsuario -= response.is_successful
        // }

        console.log(this.barcosRival)
        console.log(this.barcosUsuario)

        if (response.is_successful) {
          Swal.fire({
            icon: 'success',
            title: '¡Le diste!',
            text: 'Acabas de derribar un barco enemigo',
          });
        } else if (response.is_successful == false) {
          Swal.fire({
            icon: 'warning',
            title: 'No has atinado a un barco',
          });
        }
        // Marcar para revisión del cambio
      },
      (error) => {
        console.error('Error:', error);
        if (error.error === 'No es tu turno >:(') {
          Swal.fire({
            icon: 'error',
            title: error.error,
          });
        }
      }
    );
  }

  websocketHit(){
    this.echo.channel('barcos.'+this.juego.game.id).listen('.BarcoEvents', (data: any) => {
      console.log("WEBSOCKET HIT",data)
      
      if(data.y !== undefined && data.x !== undefined){
        if(data.x >= 0 && data.x < this.board[0].length && data.y < this.board.length){
          if(data.user_id == this.user.id){
            this.board[data.y][data.x] = '#F21B1B'
          }
        }
      }

    })
    this.echo.connect()
  }

  websocketPartida() {
    this.echo.channel('evento-juego').listen('.ActualizaJuego', (data: any) => {
      console.log("Datos recibidos del servidor:", data);
      console.log("Barcos restantes del usuario actual:", this.barcosUsuario);
      console.log("ID del usuario actual:", this.user.id);
  
      // Actualizar barcos destruidos por el usuario actual y el rival
      if (this.user.id === data.game.player1_id) {
        this.barcosUsuario -= (data.shipsDestroyedByCurrentUser > 0) ? 1 : 0;
        this.barcosRival -= (data.shipsDestroyedByOpponent > 0) ? 1 : 0;
      } else {
        this.barcosUsuario -= (data.shipsDestroyedByOpponent > 0) ? 1 : 0;
        this.barcosRival -= (data.shipsDestroyedByCurrentUser > 0) ? 1 : 0;
      }
  
      console.log("Barcos restantes del usuario actual:", this.barcosUsuario);
  
      if(data.game.status == "terminado"){
        this.router.navigate(['/dashboard'])
      }

      // Resto del código para mostrar mensajes de turno y resultado del juego
      if (data.game.next_player_id === this.user.id) {
        Swal.fire({
          position: "top-end",
          title: "Es tu turno",
          showConfirmButton: false,
          timer: 1500,
          width: 250,
          heightAuto: true
        });
      }
  
      if (data.game.winner_id) {
        if (data.game.winner_id === this.user.id) {
          Swal.fire({
            icon: 'success',
            title: '¡Felicidades! Has ganado',
          });
        } else if (data.game.winner_id !== this.user.id) {
          Swal.fire({
            icon: 'error',
            title: 'Perdiste la partida',
          });
        }
        this.router.navigate(['/dashboard']);
      }
  
      // Marcar para revisión del cambio
      this.cdr.markForCheck();
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

  generateOponentBoard(): void {
    const numCols = 5;
    const numRows = 8;

    this.oponentBoard = Array(numRows)
      .fill(null)
      .map(() => Array(numCols).fill('#fffff'));
  }
}
