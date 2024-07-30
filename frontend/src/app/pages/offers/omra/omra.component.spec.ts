import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OmraComponent } from './omra.component';

describe('OmraComponent', () => {
  let component: OmraComponent;
  let fixture: ComponentFixture<OmraComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [OmraComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(OmraComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
