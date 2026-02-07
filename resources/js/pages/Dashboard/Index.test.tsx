import '@/test/mocks';
import { render } from '@testing-library/react';
import Index from './Index';

describe('Dashboard/Index', () => {
    it('renders without crashing', () => {
        render(<Index />);
    });
});
