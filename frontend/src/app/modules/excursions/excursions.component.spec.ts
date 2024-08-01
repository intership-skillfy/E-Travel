import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ExcursionsComponent } from './excursions.component';

describe('ExcursionsComponent', () => {
  let component: ExcursionsComponent;
  let fixture: ComponentFixture<ExcursionsComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ExcursionsComponent]
    });
    fixture = TestBed.createComponent(ExcursionsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
